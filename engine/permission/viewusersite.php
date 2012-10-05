<?php

class Permission_ViewUserSite extends Permission
{        
    static $implicit = true;    
    
    static function get($entity, $user)
    {
        if ($entity instanceof Widget)
        {                    
            // need editor permission to view unpublished widgets
            if (!$entity->is_published() && !Permission_EditUserSite::is_granted($entity, $user))
            {
                return null;
            }
        }    
    
        // anyone can view approved user sites        
        $site_user = $entity->get_container_user();
        if ($site_user && $site_user->is_approved())
        {
            return new Permission_Public();
        }
        
        return parent::get($entity, $user);
    }

    static function throw_exception($entity = null)
    {
        $site_user = $entity->get_container_user();
               
        throw new PermissionDeniedException(
            ($site_user && $site_user->is_approved()) ? __('org:cantview') : __('approval:waiting'));
    }    
}