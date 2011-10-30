<?php

class Permission_ViewUserSite extends Permission
{        
    static $implicit = true;
    
    static function is_granted($entity, $user)
    {
        $site_user = $entity->get_container_user();
        if ($site_user && $site_user->is_approved())
        {
            return true;
        }
        
        return parent::is_granted($entity, $user);
    }
    
    static function throw_exception($entity = null)
    {
        $site_user = $entity->get_container_user();
               
        throw new PermissionDeniedException(
            ($site_user && $site_user->is_approved()) ? __('org:cantview') : __('approval:waiting'));
    }    
}