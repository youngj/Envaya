<?php

abstract class Permission_Secure extends Permission
{
    static function get_min_password_strength()
    {
        return PasswordStrength::Strong;
    }

    static function get_max_password_age()
    {
        return 86400 * 365;
    }
    
    /*
     * Maximum number of seconds since login before require_* functions 
     * force the user to log in again
     */
    static function get_max_login_age() 
    { 
        return 86400; 
    }
    
    /* 
     * Number of seconds when which we force login for GET requests;
     * less than get_max_login_age() so that the user has time to complete
     * and POST any forms on the page before the session becomes invalid.
     */
    static function get_near_max_login_age() 
    { 
        return (int)(static::get_max_login_age() * 0.9); 
    }
    
    static function is_valid_session()
    {
        $max_age = Request::is_post() ? static::get_max_login_age() : static::get_near_max_login_age();
        $age = Session::get_login_age();
        
        //error_log("age = $age, max_age = $max_age");
        
        return $age !== null && $age <= $max_age && Session::is_consistent_client();
    }
    
    static function require_for_entity($entity) 
    {
        if (!static::is_valid_session())
        {
            throw new PermissionDeniedException(__('login:expired'));
        }    
    
        parent::require_for_entity($entity);
    }
    
    static function require_any()
    {
        if (!static::is_valid_session())
        {
            throw new PermissionDeniedException(__('login:expired'));
        }        
        parent::require_any();
    }    
}