<?php

class Permission_EditDiscussionMessage extends Permission
{        
    static $implicit = true;
    
    static function has_for_entity($entity)
    {
        return parent::has_for_entity($entity) || $entity->is_session_owner();
    }    
}