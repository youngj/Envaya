<?php

class Permission_RegisteredUser extends Permission
{        
    static function get_any_explicit($user)
    {
        if ($user != null)
        {
            return new Permission_RegisteredUser();
        }
        return null;
    }
    
    static function get($entity, $user)
    {       
        return static::get_any_explicit($user);
    }
}