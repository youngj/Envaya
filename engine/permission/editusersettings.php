<?php

class Permission_EditUserSettings extends Permission_Secure
{        
    static $implicit = true;

    static function is_granted($entity, $user)
    {
        // prevent people with EditUserSettings permission from escalating privileges
        // by changing passwords for other accounts    
        return parent::is_granted($entity, $user) 
            && ($entity->equals($user) || !$entity->get_all_permissions());
    }
}