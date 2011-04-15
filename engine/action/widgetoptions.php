<?php

class Action_WidgetOptions extends Action
{
    function before()
    {
        $this->require_admin();
        $this->require_org();
    }

    function process_input()
    {        
        $this->validate_security_token();
        
        $widget = $this->get_widget();
        
        $widget->handler_class = get_input('handler_class');
        $widget->handler_arg = get_input('handler_arg');
        $widget->title = get_input('title');
        $widget->menu_order = (int)get_input('menu_order');
        $widget->in_menu = get_input('in_menu') == 'no' ? 0 : 1;
        $widget->save();

        system_message(__('widget:saved'));
        forward($widget->get_url());
    }
    
    function render()
    {
        $this->use_editor_layout();
        PageContext::set_translatable(false);
               
        $this->page_draw(array(
            'title' => __('widget:options'),
            'content' => view('widgets/options', array('widget' => $this->get_widget())),
        ));                       
    }
}    