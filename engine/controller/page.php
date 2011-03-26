<?php

class Controller_Page extends Controller_Profile
{   
    protected $widget;
    
    function before()
    {
        parent::before();
        
        $widgetName = $this->request->param('id');        
        $this->widget = $this->org->get_widget_by_name($widgetName); 
    }

    function action_index()
    {
        return $this->index_widget($this->widget);
    }
    
    function action_edit()
    {
        PageContext::set_translatable(false);
        $this->require_editor();
        $this->require_org();

        $org = $this->org;
        
        if (Request::is_post())
        {
            if (get_input('_draft'))
            {
                return $this->save_draft();
            }
            else
            {
                $this->save_widget();
            }
        }

        $widget = $this->widget;        
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
           
	function action_post_comment()
	{
		$widget = $this->widget;        
        if ($widget->is_active())
        {
            $this->post_comment($widget);
        }
        else
        {
            return not_found();
        }
	}           
           
    function action_options()
    {
        $this->require_admin();
        $this->require_org();
        $this->use_editor_layout();
                
        if (Request::is_post())
        {
            $this->save_options();
        }   
        
        PageContext::set_translatable(false);
               
        $title = __('widget:options');
        $body = view('widgets/options', array('widget' => $this->widget));
        
        $this->page_draw($title, view_layout("one_column", view_title($title), $body));        
    }

    private function save_options()
    {
        $this->validate_security_token();
        
        $widget = $this->widget;
        
        $widget->handler_class = get_input('handler_class');
        $widget->handler_arg = get_input('handler_arg');
        $widget->title = get_input('title');
        $widget->menu_order = (int)get_input('menu_order');
        $widget->in_menu = get_input('in_menu') == 'no' ? 0 : 1;
        $widget->save();

        system_message(__('widget:saved'));
        forward($widget->get_url());
    }       
    
    private function save_draft()
    {
        $this->request->headers['Content-Type'] = 'text/javascript';                
    
        $this->validate_security_token();        
    
        $widget = $this->widget;
        if (!$widget->guid)
        {
            $widget->disable();
            $widget->save();            
        }
        
        $revision = ContentRevision::get_recent_draft($widget);
        $revision->time_updated = time();
        $revision->content = get_input('content');                       
        $revision->save();
        
        $this->request->response = json_encode(array('guid' => $widget->guid));
    }
    
    private function save_widget()
    {
        $this->validate_security_token();        
        
        $widget = $this->widget;
    
        if (get_input('delete'))
        {
            $widget->disable();
            $widget->save();

            system_message(__('widget:delete:success'));

            forward($this->user->get_url());
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
}