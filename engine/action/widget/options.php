<?php

class Action_Widget_Options extends Action
{
    function before()
    {
        Permission_UseAdminTools::require_for_entity($this->get_widget());
    }

    function process_input()
    {        
        $widget = $this->get_widget();
        
        $widget->subtype_id = get_input('subtype_id');
        
        if (!ClassRegistry::get_class($widget->subtype_id))
        {
            throw new ValidationException("subtype_id {$widget->subtype_id} not found");
        }
        
        $widget->handler_arg = get_input('handler_arg');
        $widget->title = get_input('title');
        $widget->menu_order = (int)get_input('menu_order');
        $widget->in_menu = get_input('in_menu') == 'no' ? 0 : 1;
        $widget->save();

        SessionMessages::add(__('widget:save:success'));
        $this->redirect($widget->get_url());
    }
    
    function render()
    {
        $this->use_editor_layout();
               
        $this->page_draw(array(
            'title' => __('widget:options'),
            'content' => view('widgets/options', array('widget' => $this->get_widget())),
        ));                       
    }
}    