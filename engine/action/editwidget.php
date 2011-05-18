<?php

class Action_EditWidget extends Action
{
    function before()
    {
        parent::before();
        $this->require_editor();
        $this->require_org();
    }
    
    protected function save_draft()
    {
        $this->set_content_type('text/javascript');
    
        validate_security_token();        
    
        $widget = $this->get_widget();
        if (!$widget->guid || $widget->status == Entity::Disabled)
        {
            $widget->publish_status = Widget::Draft;
            $widget->enable();            
            $widget->save();            
        }
        
        $revision = ContentRevision::get_recent_draft($widget);
        $revision->time_updated = time();
        $revision->content = get_input('content');                       
        $revision->save();
        
        $this->set_response(json_encode(array('guid' => $widget->guid)));    
    }
    
    function process_input()
    {        
        if (get_input('_draft'))
        {
            return $this->save_draft();
        }

        validate_security_token();        
        
        $widget = $this->get_widget();
    
        if (get_input('delete'))
        {
            $widget->disable();
            $widget->save();

            SessionMessages::add(!$widget->is_section() ? __('widget:delete:success') : __('widget:delete_section:success'));            

            $this->redirect($widget->get_container_entity()->get_edit_url());
        }
        else
        {
            if (!$widget->is_enabled())
            {
                $widget->enable();
            }            
            
            $widget->publish_status = Widget::Published;
            
            $container = $widget->get_container_entity();
            if (!$container->is_enabled())
            {
                $container->enable();
                $container->save();
            }

            $widget->process_input($this);             
            
            SessionMessages::add(__('widget:save:success'));
            
            $this->redirect($widget->get_url());
        }
    }
    
    function render()
    {
        $org = $this->get_org();        
        $widget = $this->get_widget();        
        
        $cancelUrl = get_input('from') ?: $widget->get_url();

        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);

        $this->page_draw(array(
            'title' => sprintf(__('edit_item'), $widget->get_title()),
            'header' => view('widgets/edit_header', array('widget' => $widget)),
            'content' => $widget->render_edit()
        ));
    }

    protected function validate_security_token() {}    
}