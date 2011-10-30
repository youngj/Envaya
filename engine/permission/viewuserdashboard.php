<?php

abstract class Permission_ViewUserDashboard extends Permission
{        
    static $implicit = true;
    
    static function is_granted($entity, $user)
    {        
        return Permission_EditUserSite::is_granted($entity, $user)
            || Permission_EditUserSettings::is_granted($entity, $user);
    }

}