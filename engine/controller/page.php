<?php

class Controller_Page extends Controller_Profile
{   
    function action_index()
    {
        $widgetName = $this->request->param('id');        
        $widget = $this->org->get_widget_by_name($widgetName); 
        return $this->index_widget($widget);
    }
    
    function action_edit()
    {
        PageContext::set_translatable(false);
        $this->require_editor();
        $this->require_org();

        $org = $this->org;
        
        $widgetName = $this->request->param('id');

        $widget = $org->get_widget_by_name($widgetName);

        $widgetTitle = $widget->get_title();

        if ($widget->guid && $widget->is_enabled())
        {
            $title = sprintf(__("widget:edittitle"), $widgetTitle);
        }
        else
        {
            $title = sprintf(__("widget:edittitle:new"), $widgetTitle);
        }

        $cancelUrl = get_input('from') ?: $widget->get_url();

        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $body = view_layout('one_column',
            view_title($title), $widget->render_edit());

        $this->page_draw($title, $body);
    }
       
    function action_save()
    {
        $this->validate_security_token();

        if (!$this->user->can_edit())
        {
            action_error(__('org:cantedit'));
        }

        $widgetName = $this->request->param('id');

        if ($this->org)
        {
            $widget = $this->org->get_widget_by_name($widgetName);
            $this->save_widget($widget);
        }
        else
        {       
            not_found();
        }
        
        forward(get_input('from') ?: $this->user->get_url());
    }    
}