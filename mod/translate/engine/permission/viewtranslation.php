<?php

abstract class Permission_ViewTranslation extends Permission
{        
    static $implicit = true;
    
    static function get($entity, $user)
    {
        $cur = $entity;    
        
        $permission = null;
        
        // User must have permission to view all container entities in order to view the translation
        while ($cur && (!$cur instanceof UserScope))
        {
            $permission_cls = $cur->get_view_permission();           
           
            if ($permission_cls)
            {
                $permission = $permission_cls::get($cur, $user);
                if (!$permission)
                {            
                    return null;
                }
            }
            $cur = $cur->get_container_entity();
        }    
        
        if ($permission)
        {
            return $permission;
        }
        else
        {
            return new Permission_Public();
        }
    }
}