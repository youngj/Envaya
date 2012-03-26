<?php

class Permission_Public extends Permission
{        
    static function get_any_explicit($user)
    {
        return new Permission_Public();
    }
    
    static function get($entity, $user)
    {       
        return new Permission_Public();
    }
    
    function check_session_security()
    {
        // don't force people to log in again to see something that's public
    }
}