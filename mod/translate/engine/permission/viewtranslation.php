<?php

abstract class Permission_ViewTranslation extends Permission
{        
    static $implicit = true;
    
    static function is_granted($entity, $user)
    {
        $cur = $entity;    
        
        // User must have permission to view all container entities in order to view the translation
        while ($cur && (!$cur instanceof UserScope))
        {
            $permission = $cur->get_view_permission();           
           
            if ($permission && !$permission::is_granted($cur, $user))
            {
                return false;
            }
            $cur = $cur->get_container_entity();
        }    
        return true;
    }
}