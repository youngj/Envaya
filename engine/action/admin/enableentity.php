<?php

class Action_Admin_EnableEntity extends Action
{
    function before()
    {
        Permission_UseAdminTools::require_for_entity($this->param('entity'));
    }
     
    function process_input()
    {        
        $entity = $this->param('entity');        
        $entity->enable();
        $entity->save();
        
        SessionMessages::add(__('entity:enabled'));

        $next = get_input('next');
        $this->redirect($next);
    }
}