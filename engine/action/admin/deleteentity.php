<?php

class Action_Admin_DeleteEntity extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $guid = get_input('guid');
        $entity = Entity::get_by_guid($guid);

        if (!$entity)
        {
            throw new ValidationException(sprintf(__('entity:delete:fail'), $guid));
        }
        
        $entity->disable();
        $entity->save();
        SessionMessages::add(sprintf(__('entity:delete:success'), $guid));

        $next = get_input('next');
        $this->redirect($next);
    }
}    