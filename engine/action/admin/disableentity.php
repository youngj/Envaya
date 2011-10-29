<?php

class Action_Admin_DisableEntity extends Action
{
    function before()
    {
        Permission_UseAdminTools::require_for_entity($this->param('entity'));
    }
     
    function process_input()
    {        
        $entity = $this->param('entity');        
        $entity->disable();
        $entity->save();
        
        SessionMessages::add(__('entity:disabled'));

        $next = get_input('next');
        $this->redirect($next);
    }
}