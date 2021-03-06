<?php

/*
 * Default session implementation that gets the session id from a cookie, and 
 * persists session information in a database table.
 *
 * Acts as a wrapper around the standard PHP session interface that lazy-loads the 
 * session data to avoid querying the database unless necessary.
 */
class Session_Cookie implements SessionImpl
{
    private $started = false;
    private $dirty = false;
    
    static function cookie_name()
    {
        return Config::get('session_cookie_name');
    }    

    function get_logged_in_user()
    {
        return User::get_by_guid($this->get('guid'));
    }
    
    function login($user, $options)
    {            
        $this->set('guid', $user->guid);
        
        $lifetime = (@$options['persistent']) ? (60 * 60 * 24 * 60) : 0;
        $secure = Config::get('ssl_enabled');
        $httponly = true;   
        $path = '/';
        $domain = null;

        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);

        // Users privilege has been elevated, so change the session id (help prevent session hijacking)
        session_regenerate_id(true);

        if ($secure)
        {
            setcookie('https', '1', $lifetime, $path);
        }
        
        $this->set('login_time', timestamp());
        $this->set('login_ip', Request::get_client_ip());
        $this->set('login_user_agent', @$_SERVER['HTTP_USER_AGENT']);
        
        $user->reset_login_failure_count();
        $user->last_action = timestamp();
        $user->save();
        
        LogEntry::create('user:logged_in', $user);
    }
    
    function logout($user)
    {        
        if ($user)
        {
            LogEntry::create('user:logged_out', $user);
        }
    
        $this->destroy();
    }

    function get_login_age()
    {
        $login_time = $this->get('login_time');
        if ($login_time)
        {
            return timestamp() - $login_time;
        }
        return null;
    }
    
    function is_recent_login($max_age)
    {
        if (!Request::is_post())
        {
            $max_age -= 90;
        }
        
        $age = $this->get_login_age();       
        return $age !== null && $age <= $max_age;        
    }

    function is_consistent_ip()
    {
        return $this->get('login_ip') == Request::get_client_ip();
    }
    
    function is_consistent_browser()
    {
        return $this->get('login_user_agent') == Request::get_user_agent();
    }

    function is_consistent_client()
    {
        return $this->is_consistent_ip() && $this->is_consistent_browser();
    }
    
    function is_high_security()
    {
        return $this->is_recent_login(28800)
            && $this->is_consistent_browser()
            && $this->is_consistent_ip();
    }
    
    function is_medium_security()
    {       
        if ($this->override_security)
        {
            return true;
        }
        
        return $this->is_recent_login(3000000)
            && $this->is_consistent_browser();
    }    
    
    private $override_security = false;
    
    function override_security_check($override = true)
    {
        $this->override_security = $override;
    }
    
    function get($key)
    {
        if ($this->started)
        {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        }
        else if (isset($_COOKIE[static::cookie_name()]))
        {
            $this->_start();
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        }
        else
        {
            return null;
        }
    }        

    function set($key, $value)
    {
        if (!$this->started)
        {
            $this->_start();
        }
        if (is_null($value))
        {
            unset($_SESSION[$key]);
        }
        else
        {
            $_SESSION[$key] = $value;
        }
        $this->dirty = true;
    }
        
    function destroy()
    {
        @session_destroy();
        setcookie(static::cookie_name(), "", 0, '/');
        setcookie('https', '', 0, '/');
    }

    function start()
    {
        if (!$this->started)
        {
            $this->_start();
        }
    }
    
    function id()
    {
        if ($this->started)
        {
            return session_id();
        }
        if (!$this->started && @$_COOKIE[static::cookie_name()])
        {
            $this->_start();
            return session_id();
        }
        return null;
    }

    function set_dirty()
    {
        $this->dirty = true;
    }
    
    private function _start()
    {
        session_set_save_handler(
            array($this, "_session_open"), 
            array($this, "_session_close"), 
            array($this, "_session_read"),
            array($this, "_session_write"), 
            array($this, "_session_destroy"), 
            array($this, "_session_gc")
        );
        
        $cookie_name = static::cookie_name();

        session_name($cookie_name);
        
        session_start();
        
        $this->started = true;

        Hook_EndRequest::register_handler_fn(function($vars) {
            session_write_close();
        }, 10);

        if (!isset($_SESSION['__elgg_session']))
        {
            $this->set('__elgg_session', md5(microtime().rand()));
        }

        $guid = @$_SESSION['guid'];
        if ($guid)
        {
            $user = User::get_by_guid($guid);
            if (!$user)
            {
                $this->set($_SESSION['guid'], null);
            }
        }
    }
    
    static function cache_key($sessionId)
    {
        return Cache::make_key("session", $sessionId);
    }    
    
    function _session_open($save_path, $session_name)
    {
        return true;
    }    

    function _session_close()
    {
        return true;
    }
           
    function _session_read($id)
    {
        $id_sha1 = sha1($id);
    
        $cacheKey = static::cache_key($id_sha1);
        $sessionData = Cache::get_instance()->get($cacheKey);

        if ($sessionData == null)
        {
            $result = Database::get_row("SELECT * from `sessions` where id_sha1=?", array($id_sha1));
            $sessionData = ($result) ? $result->data : '';
            Cache::get_instance()->set($cacheKey, $sessionData);
        }

        return $sessionData;
    }
       
    function _session_write($id, $sess_data)
    {
        if ($this->dirty)
        {
            $id_sha1 = sha1($id);
        
            Cache::get_instance()->set(static::cache_key($id_sha1), $sess_data);

            return (Database::update("REPLACE INTO `sessions` (id_sha1, ts, data) VALUES (?,?,?)",
                    array($id_sha1, timestamp(), $sess_data))!==false);
        }
    }
    
    function _session_destroy($id)
    {
        $id_sha1 = sha1($id);
        
        Cache::get_instance()->delete(static::cache_key($id_sha1));

        return (bool)Database::delete("DELETE from `sessions` where id_sha1=?", array($id_sha1));
    }
    
    function _session_gc($maxlifetime)
    {
        $life = timestamp()-$maxlifetime;       
        return (bool)Database::delete("DELETE from `sessions` where ts<?", array($life));
    }            
}
