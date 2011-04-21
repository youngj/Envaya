<?php

class Controller_Profile extends Controller
{
    protected $org;
    protected $user;
        
    function get_org()
    {
        return $this->org;
    }

    function get_user()
    {
        return $this->user;
    }    
    
    function before()
    {
        $user = User::get_by_username($this->request->param('username'));
                
        if ($user)
        {
            $this->user = $user;

            if ($user instanceof Organization)
            {
                $this->org = $user;
            }
        }
        else
        {
            $this->not_found();
        }
    }

    function action_index()
    {
        $widgetName = $this->request->param('widgetname');
        
        if (!$widgetName)
        {
            if (!$this->org)
            {
                return $this->index_settings();
            }
            else
            {
                $widget = $this->org->query_menu_widgets()->get();
                return $this->index_widget($widget, true);
            }
        }
        else
        {
            $methodName = "index_$widgetName";
            if (method_exists($this,$methodName))
            {
                return $this->$methodName();
            }
            else if ($this->org)
            {
                $widget = $this->org->get_widget_by_name($widgetName);                        
                return $this->index_widget($widget);
            }                   
        }
        return $this->not_found();
    }
    
    function index_widget($widget, $is_home = false)
    {
        $org = $this->org;
    
        $this->require_http();
        $this->use_public_layout($widget, $is_home);
                        
        if (!$widget || !$widget->is_active())
        {
            $this->not_found();
        }
        
        if (!$org->can_view())
        {
            return $this->view_access_denied();
        }               
                
        $subtitle = $widget->get_subtitle();
        if ($is_home)
        {
            $this->page_draw_vars['full_title'] = $org->name;
        }

        if ($org->can_edit())
        {
            PageContext::get_submenu('edit')->add_item(__("widget:edit"), $widget->get_edit_url());
            PageContext::get_submenu('org_actions')->add_item(__('widget:options'), "{$widget->get_base_url()}/options");
        }

        $this->page_draw(array(
            'content' => view('widgets/view', array('widget' => $widget)),
            'title' => $subtitle,
            'show_next_steps' => $org->guid == Session::get_loggedin_userid(),
        )); 
    }        
    
    private function get_approval_message()
    {
        $org = $this->org;
    
        if ($org->approval == 0)
        {
            return __('approval:waiting');
        }
        else if ($org->approval < 0)
        {
            return __('approval:rejected');
        }
        else        
        {
            return null;
        }
    }
    
    function view_access_denied()
    {
        SessionMessages::add_error($this->get_approval_message() ?: __('org:cantview'));
        force_login();
    }

    function action_edit()
    {
        // backwards compatibility to avoid breaking links and allow editing widgets
        // at /<username>/<widgetname>/edit         
        // by forwarding to new URLs at /<username>/page/<widgetname>/edit         
     
        $widgetName = $this->request->param('widgetname');
        $widget = $this->org->get_widget_by_name($widgetName);
        if ($widget->is_active())
        {
            forward($widget->get_edit_url());
        }
        else
        {
            $this->not_found();
        }
    }
    
    protected $public_layout = false;
    
    function use_public_layout($cur_widget = null, $is_home = false)
    {
        $org = $this->org;
                
        $this->public_layout = true;
        
        $this->page_draw_vars['theme_name'] = get_input("__theme") ?: $org->theme ?: 'green';                
        $this->page_draw_vars['sitename'] = $org->name;
        $this->page_draw_vars['site_url'] = $org->get_url();
        $this->page_draw_vars['login_url'] = url_with_param(Request::instance()->full_rewritten_url(), 'login', 1);        
        
        if ($is_home || get_viewtype() != 'mobile')
        {
            $this->show_widget_menu($cur_widget);
        }
    }
    
    function show_widget_menu($cur_widget)
    {
        $org = $this->org;
        
        $widgets = $org->query_menu_widgets()->filter();
        
        foreach ($widgets as $widget)
        {
            $is_selected = $cur_widget && $cur_widget->guid == $widget->guid;
        
            PageContext::get_submenu()->add_item(
                $widget->get_title(), 
                rewrite_to_current_domain($widget->get_url()),
                $is_selected
            );
        }        
    }

    function use_editor_layout()
    {
        $this->page_draw_vars['theme_name'] = 'editor';
    }

    function require_editor()
    {
        $this->require_login();

        $user = $this->user;

        if ($user && $user->can_edit())
        {
            $this->use_editor_layout();

            return;
        }
        else if ($user)
        {
            if (Session::isloggedin())
            {
                SessionMessages::add_error(__('noaccess'));
            }        
        
            force_login();
        }
        else
        {
            $this->not_found();
        }
    }

    function require_org()
    {
        if (!$this->org)
        {
            $this->not_found();
        }
    }

    function index_add_page()
    {
        $action = new Action_AddWidget($this, $this->org);
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
            'content' => view("org/help", array('org' => $this->org)),           
        ));        
    }

    function index_dashboard()
    {    
        $this->require_editor();        
        
        PageContext::set_translatable(false);

        $user = $this->user;
        if ($user->guid == Session::get_loggedin_userid())
        {
            $title = __('dashboard:title');
        }
        else
        {
            $title = sprintf(__("dashboard:other_user"), $user->name);
        }
                
        $org = $this->org;
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
            $content = view('section', array('content' => "You are not an organization!"));
            $pre_body = '';
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
            'content' => view('org/domains', array('org' => $this->org)),
        ));
    }
    
    function index_add_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $domain_name = get_input('domain_name');
        if (OrgDomainName::query()->where('domain_name = ?', $domain_name)->count() > 0)
        {
            redirect_back_error(__('domains:duplicate'));
        }
        if (preg_match('/[^\w\.\-]/', $domain_name))
        {
            redirect_back_error(__('domains:invalid'));
        }
        
        $org_domain_name = new OrgDomainName();
        $org_domain_name->domain_name = $domain_name;
        $org_domain_name->guid = $this->org->guid;
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
        
    protected function get_pre_body($vars)
    {
        $org = $this->org;
        $preBody = '';

        if (get_input("__topbar") != "0")
        {
            if (Session::isadminloggedin())
            {
                $preBody .= view("admin/org_actions", array('entity' => $org));
            }

            if ($org->can_view() && Session::isloggedin() && Session::get_loggedin_userid() != $org->guid)
            {
                $preBody .= view("org/comm_box", array('org' => $org));
            }

            if (@$vars['show_next_steps'])
            {
                $preBody .= view("org/todo_message", array('org' => $org));
            }
        }    
        return $preBody;
    }
                
    public function page_draw($vars)
    {
        $org = $this->org;
        
        if ($org && $this->public_layout)
        {    
            if (!get_input('__theme'))
            {
                $approval_message = $this->get_approval_message();
                if ($approval_message)
                {
                    SessionMessages::add($approval_message);
                }
            }
        
            if ($org->has_custom_header())
            {
                $vars['header'] = view('org/custom_header', array(
                    'org' => $org
                ));
            }
            else
            {
                $vars['header'] = view('org/default_header', array(
                    'org' => $org,
                    'subtitle' => $vars['title'],
                ));
            }
            
            $vars['pre_body'] = $this->get_pre_body($vars);
        }
        
        return parent::page_draw($vars);
    }	
}