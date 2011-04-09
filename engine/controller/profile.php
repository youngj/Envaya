<?php

class Controller_Profile extends Controller
{
    protected $org;
    protected $user;
    
    protected $show_next_steps = false;
    
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
        $user = get_user_by_username($this->request->param('username'));
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
            not_found();
        }
    }

    function action_index()
    {
        $widgetName = $this->request->param('widgetname');
        
        if (!$this->org && $widgetName == 'home')
        {
            $widgetName = 'settings';
        }               

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
        return not_found();
    }
    
    function index_widget($widget)
    {
        $org = $this->org;
    
        $this->require_http();

        $show_menu = true;
        if (get_viewtype() == 'mobile' && $widget && $widget->widget_name != 'home')
        {
            $show_menu = false;
        }
        
        $this->use_public_layout($show_menu);

        $viewOrg = $org->can_view();

        $this->show_next_steps = $this->org->guid == Session::get_loggedin_userid();
        
        if ($widget && $widget->widget_name == 'home')
        {
            $subtitle = $widget->title ? $widget->translate_field('title', false) : $org->get_location_text(false);
            $title = '';
        }
        else if (!$widget || !$widget->is_active())
        {
            $this->org_page_not_found();
        }
        else
        {
            $subtitle = $widget->get_title();
            $title = $subtitle;
        }

        if ($org->can_edit())
        {
            PageContext::add_submenu_item(__("widget:edit"), $widget->get_edit_url(), 'edit');
            PageContext::add_submenu_item(__('widget:options'), "{$widget->get_base_url()}/options", 'org_actions');
        }

        if ($viewOrg)
        {
            $body = $this->org_view_body($subtitle, ($viewOrg ? view('widgets/view', array('widget' => $widget)) : ''));
        }
        else
        {
            $this->show_cant_view_message();
            $body = '';
        }

        $this->page_draw($title, $body);
    }        
    
    function show_cant_view_message()
    {
        if ($this->org->approval == 0)
        {
            system_message(__('approval:waiting'));
        }
        else if ($this->org->approval < 0)
        {
            system_message(__('approval:rejected'));
        }
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
            not_found();
        }
    }
    
    function use_public_layout($show_menu = true)
    {
        $org = $this->org;
        
        $this->page_draw_vars['sitename'] = $org->name;

        PageContext::set_theme(get_input("__theme") ?: $org->theme ?: 'green');
        PageContext::set_site_org($org);
        
        if ($show_menu)
        {
            foreach ($org->get_available_widgets() as $widget)
            {
                if ($widget->is_active() && $widget->in_menu)
                {
                    PageContext::add_submenu_item($widget->get_title(), rewrite_to_current_domain($widget->get_url()));
                }
            }        
        }
        
        $this->page_draw_vars['login_url'] = url_with_param(Request::instance()->full_rewritten_url(), 'login', 1);
    }

    function use_editor_layout()
    {
        PageContext::set_theme('editor');
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
                register_error(__('noaccess'));
            }        
        
            force_login();
        }
        else
        {
            not_found();
        }
    }

    function require_org()
    {
        if (!$this->org)
        {
            not_found();
        }
    }

    function index_add_page()
    {
        $action = new Action_AddPage($this);
        $action->execute();              
    }
        
    function index_design()
    {
        $action = new Action_EditDesign($this);
        $action->execute();            
    }
    
    function get_pre_body()
    {
        $org = $this->org;
        $preBody = '';

        if (get_input("__topbar") != "0")
        {
            $this->show_cant_view_message();
        
            if (Session::isadminloggedin())
            {
                $preBody .= view("admin/org_actions", array('entity' => $org));
            }

            if ($org->can_view() && Session::isloggedin() && Session::get_loggedin_userid() != $org->guid)
            {
                $preBody .= view("org/comm_box", array('entity' => $org));
            }

            if ($this->show_next_steps)
            {
                $preBody .= view("org/setupNextStep", array('entity' => $org));
            }
        }    
        return $preBody;
    }

    function index_help()
    {
        $this->require_editor();
        $this->require_org();

        $title = __("help:title");
        $area = view("org/help", array('org' => $this->org));
        $body = view_layout('one_column_padded', view_title($title), $area);
        $this->page_draw($title, $body);
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
            $area1 = view("org/dashboard", array('org' => $org));
            $area2 = view("org/setupNextStep", array('entity' => $org));                 
        }
        else if ($user->admin)
        {
            $area1 = view('admin/dashboard');
            $area2 = '';
        }
        else
        {
            $area1 = "<div class='padded'>You are not an organization!</div>";
            $area2 = '';
        }
        
        $body = view_layout("one_column", view_title($title), $area1, $area2);
        $this->page_draw($title,$body);
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
        $title = __('domains:edit');
        $area1 = view('org/domains', array('org' => $this->org));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);
    }
    
    function index_add_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $domain_name = get_input('domain_name');
        if (OrgDomainName::query()->where('domain_name = ?', $domain_name)->count() > 0)
        {
            action_error(__('domains:duplicate'));
        }
        if (preg_match('/[^\w\.\-]/', $domain_name))
        {
            action_error(__('domains:invalid'));
        }
        
        $org_domain_name = new OrgDomainName();
        $org_domain_name->domain_name = $domain_name;
        $org_domain_name->guid = $this->org->guid;
        $org_domain_name->save();
        system_message(__('domains:added'));
        forward_to_referrer();
    }
    
    function index_delete_domain()
    {
        $this->require_org();
        $this->require_admin();
        $this->validate_security_token();
        $org_domain_name = OrgDomainName::query()->where('id = ?', (int)get_input('id'))->get();
        if (!$org_domain_name)
        {
            action_error(__('domains:not_found'));
        }
        $org_domain_name->delete();
        system_message(__('domains:deleted'));
        forward_to_referrer();
    }
        
    function org_page_not_found()
    {
        $org = $this->org;
        if ($org)
        {    
            $title = __('page:notfound');
            $body = $this->org_view_body($title, "<div class='section_content padded'>".__('page:notfound:details')."</div>");
            header("HTTP/1.1 404 Not Found");
            echo page_draw($title, $body);
        }
        else
        {
            not_found();
        }
        exit;
    }   
    
    function org_view_body($subtitle, $area2)
    {
        $org = $this->org;
    
        if ($org->custom_header)
        {
            $header = view('org/custom_header', array(
                'org' => $org
            ));
        }
        else
        {
            $header = view('org/default_header', array(
                'org' => $org,
                'subtitle' => $subtitle,
            ));
        }

        $layout = "one_column_custom_header";
        if (PageContext::get_theme() == 'sidebar')
        {
            $layout= 'two_column_left_sidebar';
        }       
        
        return view_layout($layout, $header, $area2, $this->get_pre_body());
    }	
}