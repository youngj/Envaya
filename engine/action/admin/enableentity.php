<?php

class Action_Admin_EnableEntity extends Action
{
    function before()
    {
        $this->require_admin();
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