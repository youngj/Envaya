<?php

class Permission_ViewUserSettings extends Permission
{        
    static $implicit = true;
    
    static function is_granted($entity, $user)
    {
        return parent::is_granted($entity, $user) || Permission_EditUserSettings::is_granted($entity, $user);
    }
}