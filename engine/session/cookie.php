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

    function get_loggedin_user()
    {
        return User::get_by_guid($this->get('guid'));
    }
    
    function login($user, $persistent)
    {            
        $this->set('guid', $user->guid);
        
        if ($persistent)
        {
            session_set_cookie_params(60 * 60 * 24 * 60);
        }

        // Users privilege has been elevated, so change the session id (help prevent session hijacking)
        session_regenerate_id(true);
    }
    
    function logout()
    {
        $this->destroy();
    }
    
    function get($key)
    {
        if ($this->started)
        {
            return @$_SESSION[$key];
        }
        else if (@$_COOKIE[static::cookie_name()])
        {
            $this->_start();
            return @$_SESSION[$key];
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
        setcookie(static::cookie_name(), "");
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

        EventRegister::register_handler('shutdown', 'system', 'session_write_close', 10);

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
        return make_cache_key("session", $sessionId);
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
        $sessionData = get_cache()->get($cacheKey);

        if ($sessionData == null)
        {
            $result = Database::get_row("SELECT * from `sessions` where id_sha1=?", array($id_sha1));
            $sessionData = ($result) ? $result->data : '';
            get_cache()->set($cacheKey, $sessionData);
        }

        return $sessionData;
    }
       
    function _session_write($id, $sess_data)
    {
        if ($this->dirty)
        {
            $id_sha1 = sha1($id);
        
            get_cache()->set(static::cache_key($id_sha1), $sess_data);

            return (Database::update("REPLACE INTO `sessions` (id_sha1, ts, data) VALUES (?,?,?)",
                    array($id_sha1, timestamp(), $sess_data))!==false);
        }
    }
    
    function _session_destroy($id)
    {
        $id_sha1 = sha1($id);
        
        get_cache()->delete(static::cache_key($id_sha1));

        return (bool)Database::delete("DELETE from `sessions` where id_sha1=?", array($id_sha1));
    }
    
    function _session_gc($maxlifetime)
    {
        $life = timestamp()-$maxlifetime;       
        return (bool)Database::delete("DELETE from `sessions` where ts<?", array($life));
    }            
}