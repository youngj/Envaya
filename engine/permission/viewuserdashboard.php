<?php

abstract class Permission_ViewUserDashboard extends Permission
{        
    static $implicit = true;
    
    static function get($entity, $user)
    {        
        return Permission_EditUserSite::get($entity, $user)
            ?: Permission_EditUserSettings::get($entity, $user);
    }

}