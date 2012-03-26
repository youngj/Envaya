<?php

class Permission_EditUserSite extends Permission
{        
    static $implicit = true;
    
    static function get($entity, $user)
    {
        $site_user = $entity->get_container_user();
        if ($site_user && $site_user instanceof Person)
        {
            return null;
        }
        
        return parent::get($entity, $user);
    }
}