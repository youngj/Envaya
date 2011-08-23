<?php

/*
 * Interface for getting and setting session data, which is persisted in the database.
 * Acts as a wrapper around the standard PHP session interface that lazy-loads the 
 * session data to avoid querying the database unless necessary.
 */ 
class Session
{
    private static $started = false;
    private static $dirty = false;

    static function get($key)
    {
        if (static::$started)
        {
            return @$_SESSION[$key];
        }
        else if (@$_COOKIE[static::cookie_name()])
        {
            static::_start();
            return @$_SESSION[$key];
        }
        else
        {
            return null;
        }
    }    
    
    static function cookie_name()
    {
        return Config::get('session_cookie_name');
    }

    static function destroy()
    {
        @session_destroy();
        setcookie(static::cookie_name(), "");
    }

    static function start()
    {
        if (!static::$started)
        {
            static::_start();
        }
    }
    
    static function id()
    {
        if (static::$started)
        {
            return session_id();
        }
        if (!static::$started && @$_COOKIE[static::cookie_name()])
        {
            static::_start();
            return session_id();
        }
        return null;
    }
    
    static function regenerate_id()
    {
        session_regenerate_id(true);
    }

    static function save_input()
    {
        static::set('input', $_POST);
    }

    private static function _start()
    {
        session_set_save_handler(
            array('Session', "_session_open"), 
            array('Session', "_session_close"), 
            array('Session', "_session_read"),
            array('Session', "_session_write"), 
            array('Session', "_session_destroy"), 
            array('Session', "_session_gc")
        );
        
        $cookie_name = static::cookie_name();

        session_name($cookie_name);
        
        session_start();
        
        static::$started = true;

        EventRegister::register_handler('shutdown', 'system', 'session_write_close', 10);

        if (!isset($_SESSION['__elgg_session']))
        {
            static::set('__elgg_session', md5(microtime().rand()));
        }

        $guid = @$_SESSION['guid'];
        if ($guid)
        {
            $user = User::get_by_guid($guid);
            if (!$user)
            {
                static::set($_SESSION['guid'], null);
            }
        }
    }
    
    static function set_dirty($dirty = true)
    {
        static::$dirty = $dirty;
    }

    static function is_dirty()
    {
        return static::$dirty;
    }

    static function set($key, $value)
    {
        if (!static::$started)
        {
            static::_start();
        }
        if (is_null($value))
        {
            unset($_SESSION[$key]);
        }
        else
        {
            $_SESSION[$key] = $value;
        }
        static::$dirty = true;
    }

    static function cache_key($sessionId)
    {
        return make_cache_key("session", $sessionId);
    }    
    
    static function _session_open($save_path, $session_name)
    {
        return true;
    }    

    static function _session_close()
    {
        return true;
    }
           
    static function _session_read($id)
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
       
    static function _session_write($id, $sess_data)
    {
        if (Session::is_dirty())
        {
            $id_sha1 = sha1($id);
        
            get_cache()->set(static::cache_key($id_sha1), $sess_data);

            return (Database::update("REPLACE INTO `sessions` (id_sha1, ts, data) VALUES (?,?,?)",
                    array($id_sha1, time(), $sess_data))!==false);
        }
    }
    
    static function _session_destroy($id)
    {
        $id_sha1 = sha1($id);
        
        get_cache()->delete(static::cache_key($id_sha1));

        return (bool)Database::delete("DELETE from `sessions` where id_sha1=?", array($id_sha1));
    }
    
    static function _session_gc($maxlifetime)
    {
        $life = time()-$maxlifetime;
        
        return (bool)Database::delete("DELETE from `sessions` where ts<?", array($life));
    }
    
    static function get_loggedin_user()
    {
        return User::get_by_guid(static::get('guid'));
    }

    static function get_loggedin_userid()
    {
        $user = static::get_loggedin_user();
        return ($user) ? $user->guid : 0;       
    }

    static function isloggedin()
    {
        return static::get_loggedin_user() != null;
    }

    static function isadminloggedin()
    {    
        $user = static::get_loggedin_user();
        return ($user && $user->admin);
    }    
    
    /**
     * Logs in a specified User. 
     *
     * @param User $user A valid user object
     * @param boolean $persistent Should this be a persistent login?
     * @return true|false Whether login was successful
     */
    static function login($user, $persistent = false)
    {
        if ($user->check_rate_limit_exceeded())
            return false;

        Session::set('guid', $user->guid);

        if ($persistent)
        {
            session_set_cookie_params(60 * 60 * 24 * 60);
        }

        // Users privilege has been elevated, so change the session id (help prevent session hijacking)
        Session::regenerate_id();
        
        EventRegister::trigger_event('login','user',$user);
        
        $user->reset_login_failure_count();
        $user->last_action = time();
        $user->save();    

        return true;
    }

    static function logout()
    {
        $curUser = Session::get_loggedin_user();
        if ($curUser)
        {
            EventRegister::trigger_event('logout','user',$curUser);
        }

        Session::destroy();
        return true;
    }    
}
