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
    
    function is_high_security()
    {
        return true;
    }
}