<?php

class Action_Admin_DisableEntity extends Action
{
    function before()
    {
        $this->require_admin();
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