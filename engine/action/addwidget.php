<?php

class Action_AddWidget extends Action
{
    function before()
    {
        $this->require_editor();
        $this->require_org();        
    }
     
    function process_input()
    {
        $container = $this->get_widget();
        
        $title = get_input('title');
        if (!$title)
        {
            SessionMessages::add_error(__('widget:no_title'));            
            return $this->render();
        }
        
        if (!$container->is_enabled())
        {
            $container->enable();
            $container->save();
        }
                
        $widget = $container->get_widget_by_name(get_input('uniqid'));
        $widget->save_input();
        
        SessionMessages::add(__('widget:save:success'));        
        
        forward($container->get_url());
    }

    function render()
    {
        $container = $this->get_widget();
        
        $cancelUrl = get_input('from') ?: $container->get_url();
        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);
        
        $this->page_draw(array(
            'title' => sprintf(__("widget:add_section"), $container->get_title()),
            'content' => view("widgets/add_section", array('widget' => $container))
        ));
    }
}