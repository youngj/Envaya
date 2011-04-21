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
    
        validate_security_token();        
    
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

        validate_security_token();        
        
        $widget = $this->get_widget();
    
        if (get_input('delete'))
        {
            $widget->disable();
            $widget->save();

            SessionMessages::add($widget->is_page() ? __('widget:delete:success') : __('widget:delete_section:success'));            

            forward($this->get_org()->get_url());
        }
        else
        {
            if (!$widget->is_enabled())
            {
                $widget->enable();
            }            
            $container = $widget->get_container_entity();
            if (!$container->is_enabled())
            {
                $container->enable();
                $container->save();
            }

            try
            {
                $widget->process_input($this);             
            }
            catch (ValidationException $ex)
            {
                return redirect_back_error($ex->getMessage());
            }                
            catch (NotFoundException $ex)
            {
                $this->not_found();
            }
            
            SessionMessages::add(__('widget:save:success'));
            
            forward($widget->get_url());
        }
    }
    
    function render()
    {
        $org = $this->get_org();        
        $widget = $this->get_widget();        
        
        $widgetTitle = $widget->get_title();

        if ($widget->is_page())
        {
            $title = sprintf(__("widget:edit_title"), $widgetTitle);
        }
        else
        {
            $title = sprintf(__("widget:edit_section_title"), $widget->get_container_entity()->get_title(), $widgetTitle);
        }
        

        $cancelUrl = get_input('from') ?: $widget->get_url();

        PageContext::set_translatable(false);
        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);

        try
        {
            $this->page_draw(array(
                'title' => $title,
                'content' => $widget->render_edit()
            ));
        }
        catch (NotFoundException $ex)
        {
            $this->not_found();
        }
    }

    protected function validate_security_token() {}    
}