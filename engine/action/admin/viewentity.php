<?php

class Action_Admin_ViewEntity extends Action
{
    function before()
    {
        Permission_UseAdminTools::require_for_entity($this->param('entity'));
    }     
    
    function render()
    {
        $entity = $this->param('entity');

        $this->page_draw(array(
            'header' => view('admin/entity_header', array(
                'entity' => $entity,
            )),
            'title' => $entity->get_title(),
            'content' => view('admin/view_entity', array(
                'entity' => $entity
            ))
        ));
    }
}