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
        static::$loaded_user = false;
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
    }    
        
    static function is_high_security()
    {
        return static::get_instance()->is_high_security();
    }
    
    static function is_medium_security()
    {
        return static::get_instance()->is_medium_security();    
    }

    static function logout()
    {
        $user = static::get_logged_in_user();
    
        static::$loaded_user = false;               
        static::get_instance()->logout($user);
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
