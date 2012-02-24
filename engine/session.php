<?php

/*
 * Interface for getting and setting session data.
 *
 * Main implementation is Session_Cookie, but this can be replaced e.g. to store session as SMS_State.
 */ 
class Session
{
    private static $loaded_user = false;
    private static $user;        
    private static $instance;
    
    static function set_instance($instance)
    {
        static::$instance = $instance;
    }
    
    static function get_instance()
    {
        if (!isset(static::$instance))
        {
            static::$instance = new Session_Cookie();
        }
        return static::$instance;
    }

    static function get($key)
    {
        $instance = static::$instance ?: static::get_instance();
        return $instance->get($key);
    }
    
    static function set($key, $value)
    {
        return static::get_instance()->set($key, $value);
    }    
    
    static function destroy()
    {
        return static::get_instance()->destroy();
    }

    static function start()
    {
        return static::get_instance()->start();
    }
    
    static function id()
    {
        return static::get_instance()->id();
    }
    
    static function save_input()
    {
        static::set('input', $_POST);
    }
    
    static function get_logged_in_user()
    {
        if (!static::$loaded_user)
        {
            static::$loaded_user = true;
            static::$user = static::get_instance()->get_logged_in_user();
        }
        return static::$user;
    }
    
    static function is_logged_in()
    {
        return static::get_logged_in_user() != null;
    }

    static function login($user, $options = null)
    {
        static::$loaded_user = false;
        static::get_instance()->login($user, $options);

        static::set('login_time', timestamp());
        static::set('login_ip', Request::get_client_ip());
        static::set('login_user_agent', @$_SERVER['HTTP_USER_AGENT']);
        
        $user->reset_login_failure_count();
        $user->last_action = timestamp();
        $user->save();    
        
        LogEntry::create('user:logged_in', $user);
    }    
    
    static function get_login_age()
    {
        $login_time = static::get('login_time');
        if ($login_time)
        {
            return timestamp() - $login_time;
        }
        return null;
    }
    
    static function is_consistent_client()
    {
        return static::get('login_ip') == Request::get_client_ip()
            && static::get('login_user_agent') == $_SERVER['HTTP_USER_AGENT'];
    }
    
    static function logout()
    {
        $user = Session::get_logged_in_user();
        if ($user)
        {
            LogEntry::create('user:logged_out', $user);
        }
        static::$loaded_user = false;        
        
        static::get_instance()->logout();
        return true;
    }
    
    static function get_entity_by_uniqid($uniqid)
    {
        $uniqids = Session::get("uniqids");
        if (isset($uniqids) && isset($uniqids[$uniqid]))
        {
            return Entity::get_by_guid($uniqids[$uniqid]);
        }
        return null;
    }    
    
    static function cache_uniqid($uniqid, $entity)
    {
        if (!$uniqid || isset($uniqid[64]))
        {        
            throw new ValidationException("Invalid uniqid");
        }
        
        $uniqids = Session::get("uniqids");
        if (!isset($uniqids))
        {
            $uniqids = array();
        }
        $uniqids[$uniqid] = $entity->guid;
        Session::set("uniqids", $uniqids);
    }
}
