<?php

class Action_Widget_Edit extends Action
{
    function before()
    {
        parent::before();
        
        if (get_input('_draft'))
        {
            $this->set_content_type('text/javascript');
        }
        
        Permission_EditUserSite::require_for_entity($this->get_widget());
    }
    
    protected function save_draft()
    {
        $widget = $this->get_widget();
        $widget->save_draft(get_input('content'));                       
        
        $this->set_content(json_encode(array('guid' => $widget->guid)));    
    }
    
    function process_input()
    {        
        if (get_input('_draft'))
        {
            return $this->save_draft();
        }

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
            
            $response = $this->get_response();            
            if ($response->status == 200 && !$response->content)
            {            
                SessionMessages::add(__('widget:save:success'));            
                $this->redirect($widget->get_url());
            }
        }
    }
    
    function render()
    {
        $widget = $this->get_widget();        
        
        $this->use_editor_layout();
        
        
        $cancelUrl = get_input('from') ?: $widget->get_url();

        PageContext::get_submenu('edit')->add_link(__("canceledit"), $cancelUrl);

        $this->page_draw(array(
            'title' => sprintf(__('edit_item'), $widget->get_title()),
            'header' => view('widgets/edit_header', array('widget' => $widget)),
            'content' => $widget->render_edit()
        ));
    }
}