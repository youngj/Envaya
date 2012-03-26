<?php

class Permission_ViewUserSettings extends Permission
{        
    static $implicit = true;
    
    static function get($entity, $user)
    {
        return parent::get($entity, $user) ?: Permission_EditUserSettings::get($entity, $user);
    }
}