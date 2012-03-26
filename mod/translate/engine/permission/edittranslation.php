<?php

class Permission_EditTranslation extends Permission
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
}