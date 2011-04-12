<?php

class Action_EditWidget extends Action
{
    function before()
    {
        $this->require_editor();
        $this->require_org();
    }
    
    protected function save_draft()
    {
        $request = $this->get_request();
        $request->headers['Content-Type'] = 'text/javascript';                
    
        $this->validate_security_token();        
    
        $widget = $this->get_widget();
        if (!$widget->guid || $widget->status == EntityStatus::Disabled)
        {
            $widget->set_status(EntityStatus::Draft);
            $widget->save();            
        }
        
        $revision = ContentRevision::get_recent_draft($widget);
        $revision->time_updated = time();
        $revision->content = get_input('content');                       
        $revision->save();
        
        $request->response = json_encode(array('guid' => $widget->guid));    
    }

    function process_input()
    {        
        if (get_input('_draft'))
        {
            return $this->save_draft();
        }

        $this->validate_security_token();        
        
        $widget = $this->get_widget();
    
        if (get_input('delete'))
        {
            $widget->disable();
            $widget->save();

            system_message(__('widget:delete:success'));

            forward($this->get_org()->get_url());
        }
        else
        {
            if (!$widget->is_enabled())
            {
                $widget->enable();
            }

            $widget->save_input();
            
            system_message(__('widget:save:success'));
            forward($widget->get_url());
        }
    }
    
    function render()
    {
        $org = $this->get_org();        
        $widget = $this->get_widget();        
        
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

        PageContext::set_translatable(false);
        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);

        $body = view_layout('one_column',
            view_title($title), $widget->render_edit());

        $this->page_draw($title, $body);
    }
}    