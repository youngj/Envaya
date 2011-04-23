<?php

class Action_AddWidget extends Action
{
    protected $container; // WidgetContainer

    function __construct($controller, $container)
    {
        parent::__construct($controller);
        $this->container = $container;
    }

    function before()
    {
        $this->require_editor();
        $this->require_org();        
    }
     
    function process_input()
    {
        $container = $this->container;                        
        if (!$container->is_enabled())
        {
            $container->enable();
            $container->save();
        }
        
        $widget = $container->new_child_widget_from_input();        
        
        $draft = (int)get_input('_draft');
        
        $widget->publish_status = $draft ? Widget::Draft : Widget::Published;       
        
        $widget->process_input($this);
        
        if ($draft)
        {
            forward($widget->get_edit_url());        
        }        
        
        SessionMessages::add(__('widget:save:success'));                
        forward($widget->get_url());
    }

    function render()
    {
        $container = $this->container;
        
        $cancelUrl = get_input('from') ?: $container->get_url();
        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);
        
        $this->page_draw(array(
            'title' => $container->render_add_child_title(),
            'content' => $container->render_add_child()
        ));
    }
}