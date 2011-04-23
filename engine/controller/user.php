<?php

/*
 * Base class for controllers that work in the context of a User's site.
 * The User is determined from the username in the URL.
 *
 * URL: /<username>[...]
 */
abstract class Controller_User extends Controller
{
    protected $org;
    protected $user;
    protected $public_layout = false;
        
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
   
    function index_widget($widget)
    {
        $org = $this->org;
    
        $this->prefer_http();      
        $this->use_public_layout($widget);
        $this->allow_content_translation();
        
        if ($widget && $widget->is_enabled())
        {
            $this->allow_view_types($widget->get_view_types());
        }
        else
        {
            $this->allow_view_types(null);
        }
                        
        if (!$widget || !$widget->is_active())
        {
            $this->not_found();
        }
        
        if (!$org->can_view())
        {
            return $this->view_access_denied();
        }               
                
        if ($org->can_edit())
        {
            PageContext::get_submenu('edit')->add_item(__("widget:edit"), $widget->get_edit_url());
            PageContext::get_submenu('org_actions')->add_item(__('widget:options'), "{$widget->get_base_url()}/options");
        }

        $this->page_draw(array(
            'content' => $widget->render_view(array('is_primary' => true)),
            'title' => $widget->get_subtitle(),
            'show_next_steps' => $org->guid == Session::get_loggedin_userid(),
        )); 
    }           
   
    function view_access_denied()
    {
        SessionMessages::add_error($this->get_approval_message() ?: __('org:cantview'));
        force_login();
    }
        
    function use_public_layout($cur_widget = null)
    {
        $org = $this->org;
                
        $this->public_layout = true;
        
        $this->page_draw_vars['theme_name'] = get_input("__theme") ?: $org->theme ?: 'green';                
        $this->page_draw_vars['sitename'] = $org->name;
        $this->page_draw_vars['site_url'] = $org->get_url();
        $this->page_draw_vars['login_url'] = url_with_param(Request::instance()->full_rewritten_url(), 'login', 1);
        
        $this->show_site_menu($cur_widget);
    }
    
    private function show_site_menu($cur_widget)
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