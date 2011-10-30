<?php

class Permission_EditUserSite extends Permission
{        
    static $implicit = true;
    
    static function is_granted($entity, $user)
    {
        $site_user = $entity->get_container_user();
        if ($site_user && $site_user instanceof Person)
        {
            return false;
        }
        
        return parent::is_granted($entity, $user);
    }
}