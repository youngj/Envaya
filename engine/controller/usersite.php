<?php

/*
 * Controller for the top level of a user's site.
 *
 * widget_name may refer to an actual widget name for pre-defined widgets
 * (an alias for /<username>/page/<widget_name>[/<action>])
 *
 * URL: /<username>[/<widget_name>[/<action>]]
 */
class Controller_UserSite extends Controller_User
{
    static $routes = array(
        array(
            'regex' => '(/)?$',
            'action' => 'action_index',
        ),
        array(
            'regex' => '/(post|widget)/(?P<container_guid>\d+)\.(?P<widget_name>\w+)',
            'controller' => 'Controller_Widget',
            'before' => 'init_widget_from_container',
        ),
        array(
            'regex' => '/(post|widget)/((?P<slug>[\w\-]+)\,)?(?P<widget_guid>\d+)',
            'controller' => 'Controller_Widget',
            'before' => 'init_widget_from_guid',
        ),    
        array(
            'regex' => '/page/(?P<widget_name>[\w\-]+)',
            'controller' => 'Controller_Widget',
            'before' => 'init_widget_from_name',
        ),
        array(
            'regex' => '/(?P<action>\w+)\b',
        ),
        array(
            'regex' => '/(?P<widget_name>[\w\-]+)(/(?P<action>\w+))?',
            'defaults' => array('action' => 'view'),
            'action' => 'action_widget_<action>',
        )
    );

    function action_index()
    {
        $org = $this->get_org();
        
        if (!$org)
        {
            return $this->action_settings();
        }
        else
        {                             
            $this->page_draw_vars['is_site_home'] = true;
            
            $home_widget = $org->query_menu_widgets()->get();             
            return $this->index_widget($home_widget);
        }
    }
    
    protected function init_widget_from_guid()
    {
        $guid = $this->param('widget_guid');                   
        return $this->init_widget(Widget::get_by_guid($guid, true));
    }
     
    protected function init_widget_from_container()
    {
        $container_guid = $this->param('container_guid');
        $widget_name = $this->param('widget_name');
     
        $container = Widget::get_by_guid($container_guid, true);
        return $this->init_widget($container ? $container->get_widget_by_name($widget_name) : null);
    }

    protected function init_widget_from_name()
    {
        $widgetName = $this->param('widget_name');
        $this->init_widget($this->get_user()->get_widget_by_name($widgetName));
    }    
     
    protected function init_widget($widget)
    {
        if ($widget && $widget->get_container_user()->guid == $this->get_user()->guid)
        {
            $this->params['widget'] = $widget;
        }
        else
        {
            throw new NotFoundException();
        }       
    }    
    
    function action_widget_view()
    {    
        $org = $this->get_org();
        $widgetName = $this->param('widget_name');        
               
        if ($org)
        {            
            $widget = $org->get_widget_by_name($widgetName);                        

            $home_widget = $org->query_menu_widgets()->get();
            if ($home_widget && $widget && $home_widget->guid == $widget->guid)
            {                
                $this->page_draw_vars['is_site_home'] = true;
            }
            
            return $this->index_widget($widget);
        }                   
        else
        {        
            throw new NotFoundException();
        }
    }
        
    function action_widget_edit()
    {    
        // backwards compatibility to avoid breaking links and allow editing widgets
        // at /<username>/<widget_name>/edit         
        // by forwarding to new URLs at /<username>/page/<widget_name>/edit         
     
        $org = $this->get_org();
        if (!$org)
        {
            throw new NotFoundException();
        }
        
        $widgetName = $this->param('widget_name');
        $widget = $org->get_widget_by_name($widgetName);
        
        if (!$widget->is_enabled())
        {
            throw new NotFoundException();            
        }
        $this->redirect($widget->get_edit_url());
    }

    function action_dashboard()
    {    
        $this->require_site_editor();        
        $this->allow_view_types(null);        
        
        $vars = array();

        $user = $this->get_user();
        if ($user->guid == Session::get_loggedin_userid())
        {
            $vars['title'] = __('edit_site');
        }
        else
        {
            $vars['title'] = sprintf(__('edit_item'), $user->name);
        }
                
        $org = $this->get_org();
        if ($org)
        {            
            $vars['content'] = view("org/dashboard", array('org' => $org));
            $vars['messages'] = view('messages/dashboard', array('org' => $org));
        }
        else if ($user->admin)
        {
            $vars['content'] = view('admin/dashboard');
        }
        else
        {
            throw new RedirectException('', $user->get_url());
        }
        
        $this->page_draw($vars);
    }
    
    function action_password()
    {
        $action = new Action_User_ChangePassword($this);
        $action->execute();    
    }

    function action_username()
    {
        $action = new Action_User_ChangeUsername($this);
        $action->execute();
    }

    function action_settings()
    {    
        $action = new Action_User_Settings($this);
        $action->execute();
    }

    function action_addphotos()
    {
        $action = new Action_User_AddPhotos($this);
        $action->execute();        
    }
            
    function action_send_message()
    {
        $action = new Action_User_SendMessage($this);
        $action->execute();   
    }
    
    function action_domains()
    {
        $this->require_admin();
        $this->use_editor_layout();
        
        $this->page_draw(array(
            'title' => __('domains:edit'),
            'content' => view('account/domains', array('user' => $this->get_user())),
        ));
    }
    
    function action_add_domain()
    {
        $action = new Action_User_AddDomain($this);
        $action->execute();        
    }
    
    function action_delete_domain()
    {
        $action = new Action_User_DeleteDomain($this);
        $action->execute();
    }

    function action_share()
    {
        $action = new Action_User_Share($this);
        $action->execute();
    }
    
    function action_custom_design()
    {
        $action = new Action_User_CustomDesign($this);
        $action->execute();
    }
    
    function action_add_page()
    {
        $action = new Action_Widget_Add($this, $this->get_org());
        $action->execute();
    }
        
    function action_design()
    {
        $action = new Action_User_Design($this);
        $action->execute();
    }
       
}