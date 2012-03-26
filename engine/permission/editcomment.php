<?php

class Permission_EditComment extends Permission
{        
    static $implicit = true;

    static function get($entity, $user)
    {
        if ($user && $user->equals($entity->get_owner_entity()))
        {
            return new Permission_Implicit();
        }
    
        return parent::get($entity, $user);
    }
    
    static function get_for_current_user($entity)
    {
        if ($entity instanceof Comment && $entity->is_session_owner())
        {
            return new Permission_EditComment();
        }
        else
        {    
            return parent::get_for_current_user($entity);
        }
    }            
}