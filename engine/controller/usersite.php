<?php

/*
 * Controller for the top level of a user's site.
 *
 * widget_name may refer to an actual widget name for pre-defined widgets
 * (an alias for /<username>/page/<widget_name>[/<action>])
 * or may actually be an action defined here with prefix 'index_'.
 *
 * URL: /<username>[/<widget_name>[/<action>]]
 */
class Controller_UserSite extends Controller_User
{
    static $routes = array(
        array(
            'regex' => '$',
            'defaults' => array('widget_name' => ''), 
        ),    
        array(
            'regex' => '/(?P<controller>post|page|topic|widget)\b',
        ),        
        array(
            'regex' => '/(?P<widget_name>[\w\-]*)(/(?P<action>\w+))?',
        )
    );

    function action_index()
    {
        $org = $this->get_org();
    
        $widgetName = $this->param('widget_name');
        
        if (!$widgetName)
        {
            if (!$org)
            {
                return $this->index_settings();
            }
            else
            {                             
                $this->page_draw_vars['full_title'] = $org->name;
                $this->page_draw_vars['is_site_home'] = true;
            
                $widget = $org->query_menu_widgets()->get();             
                return $this->index_widget($widget);
            }
        }
        else
        {
            $methodName = "index_$widgetName";
            if (method_exists($this,$methodName))
            {
                return $this->$methodName();
            }
            else if ($org)
            {
                $widget = $org->get_widget_by_name($widgetName);                        
                return $this->index_widget($widget);
            }                   
        }
        return $this->not_found();
    }
        
    function action_edit()
    {
        // backwards compatibility to avoid breaking links and allow editing widgets
        // at /<username>/<widget_name>/edit         
        // by forwarding to new URLs at /<username>/page/<widget_name>/edit         
     
        $widgetName = $this->param('widget_name');
        $widget = $this->get_org()->get_widget_by_name($widgetName);
        if ($widget->is_enabled())
        {
            forward($widget->get_edit_url());
        }
        else
        {
            $this->not_found();
        }
    }
    
    function index_add_page()
    {
        $action = new Action_AddWidget($this, $this->get_org());
        $action->execute();
    }
        
    function index_design()
    {
        $action = new Action_EditDesign($this);
        $action->execute();            
    }
    
    function index_help()
    {
        $this->require_editor();
        $this->require_org();
        
        $this->page_draw(array(
            'title' => __("help:title"),
            'content' => view("org/help", array('org' => $this->get_org())),           
        ));        
    }

    function index_dashboard()
    {    
        $this->require_editor();        
        $this->allow_view_types(null);        

        $user = $this->get_user();
        if ($user->guid == Session::get_loggedin_userid())
        {
            $title = __('edit_site');
        }
        else
        {
            $title = sprintf(__('edit_item'), $user->name);
        }
                
        $org = $this->get_org();
        if ($org)
        {            
            $content = view("org/dashboard", array('org' => $org));
            $pre_body = view("org/todo_message", array('org' => $org));                 
        }
        else if ($user->admin)
        {
            $content = view('admin/dashboard');
            $pre_body = '';
        }
        else
        {
            forward($user->get_url());
        }
        
        $this->page_draw(array(
            'title' => $title,
            'content' => $content,
            'pre_body' => $pre_body
        ));
    }

    function index_username()
    {
        $action = new Action_Admin_ChangeUsername($this);
        $action->execute();
    }

    function index_settings()
    {    
        $action = new Action_Settings($this);
        $action->execute();
    }

    function index_addphotos()
    {
        $action = new Action_AddPhotos($this);
        $action->execute();        
    }
            
    function index_send_message()
    {
        $action = new Action_SendMessage($this);
        $action->execute();   
    }
    
    function index_domains()
    {
        $this->require_org();
        $this->require_admin();
        $this->use_editor_layout();
        
        $this->page_draw(array(
            'title' => __('domains:edit'),
            'content' => view('org/domains', array('org' => $this->get_org())),
        ));
    }
    
    function index_add_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $domain_name = get_input('domain_name');
        if (OrgDomainName::query()->where('domain_name = ?', $domain_name)->exists())
        {
            redirect_back_error(__('domains:duplicate'));
        }
        if (preg_match('/[^\w\.\-]/', $domain_name))
        {
            redirect_back_error(__('domains:invalid'));
        }
        
        $org_domain_name = new OrgDomainName();
        $org_domain_name->domain_name = $domain_name;
        $org_domain_name->guid = $this->get_org()->guid;
        $org_domain_name->save();
        SessionMessages::add(__('domains:added'));
        redirect_back();
    }
    
    function index_delete_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $org_domain_name = OrgDomainName::query()->where('id = ?', (int)get_input('id'))->get();
        if (!$org_domain_name)
        {
            redirect_back_error(__('domains:not_found'));
        }
        $org_domain_name->delete();
        SessionMessages::add(__('domains:deleted'));
        redirect_back();
    }
}