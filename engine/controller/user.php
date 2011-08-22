<?php

/*
 * Base class for controllers that work in the context of a User's site.
 * The User is determined from the username in the URL.
 *
 * URL: /<username>[...]
 */
abstract class Controller_User extends Controller
{
    static $routes = array();

    protected $public_layout = false;
        
    function get_org()
    {
        return $this->param('org');
    }

    function get_user()
    {
        return $this->param('user');
    }       
        
    private function get_approval_message()
    {
        $org = $this->get_org();
    
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
   
    public function execute($uri)
    {
        try
        {           
            parent::execute($uri);
        }
        catch (NotFoundException $ex)
        {
            $this->not_found();
        }
    }   
   
    function index_widget($widget)
    {
        $org = $this->get_org();
    
        $this->prefer_http();      
        $this->allow_content_translation();        
        $this->use_public_layout($widget);        
        
        if ($widget && $widget->is_enabled())
        {
            $this->allow_view_types($widget->get_view_types());
        }
        else
        {
            $this->allow_view_types(null);
        }
                        
        if (!$widget || !$widget->is_enabled() || $widget->publish_status != Widget::Published)
        {
            throw new NotFoundException();
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
        
        $container = $widget->get_container_entity();         
        $content = $container->render_child_view($widget, array('is_primary' => true));
        
        $this->page_draw(array(
            'content' => $content,
            'title' => $widget->get_title(),
            'show_next_steps' => $org->guid == Session::get_loggedin_userid(),
        )); 
    }           
   
    function view_access_denied()
    {
        $this->force_login($this->get_approval_message() ?: __('org:cantview'));
    }
        
    function use_public_layout($cur_widget = null)
    {
        $org = $this->get_org();
                
        $this->public_layout = true;
                
        $theme_name = get_input("__theme") ?: $org->get_design_setting('theme_name') ?: Config::get('fallback_theme');
        
        $this->page_draw_vars['design'] = $org->get_design_settings();
        $this->page_draw_vars['tagline'] = $org->get_design_setting('tagline');
        $this->page_draw_vars['theme_name'] = $theme_name;
        $this->page_draw_vars['site_name'] = $org->name;
        $this->page_draw_vars['site_username'] = $org->username;
        $this->page_draw_vars['site_approved'] = $org->is_approved();
        $this->page_draw_vars['site_url'] = $org->get_url();     
        $this->page_draw_vars['logo'] = view('org/icon', array('org' => $org, 'size' => 'medium'));          
        $this->page_draw_vars['login_url'] = url_with_param($this->full_rewritten_url(), 'login', 1);
        
        if (Views::get_request_type() == 'default')
        {
            $theme = Theme::get($theme_name);            
            Views::set_current_type($theme->get_viewtype());         
        }
        
        $this->show_site_menu($cur_widget);
    }
    
    private function show_site_menu($cur_widget)
    {
        $org = $this->get_org();
        
        $widgets = $org->query_menu_widgets()
            ->columns('guid,container_guid,owner_guid,language,widget_name,subclass,handler_arg,title')
            ->filter();
        
        foreach ($widgets as $widget)
        {
            $is_selected = $cur_widget && $cur_widget->guid == $widget->guid;
        
            PageContext::get_submenu()->add_item(
                $widget->get_title(), 
                $widget->get_url(),
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

        $user = $this->get_user();

        if ($user && $user->can_edit())
        {
            $this->use_editor_layout();
        }
        else if ($user)
        {
            $this->force_login(Session::isloggedin() ? __('page:noaccess') : '');   
        }
        else
        {
            throw new NotFoundException();
        }
    }

    function require_org()
    {
        if (!$this->get_org())
        {
            throw new NotFoundException();
        }
    }
    
    protected function get_messages($vars)
    {
        $org = $this->get_org();
        $messages = '';

        if (get_input("__topbar") != "0")
        {
            if (Session::isadminloggedin())
            {
                $messages .= view("admin/org_actions", array('org' => $org));
            }

            if ($org->can_view() && Session::isloggedin() && Session::get_loggedin_userid() != $org->guid)
            {
                $messages .= view("org/comm_box", array('org' => $org));
            }

            if (@$vars['show_next_steps'])
            {
                $messages .= view("org/todo_message", array('org' => $org));
            }
        }    
        
        $messages .= SessionMessages::view_all();
        
        return $messages;
    }
                
    public function prepare_page_draw_vars(&$vars)
    {
        $org = $this->get_org();
        
        $is_public = ($org && $this->public_layout);
        
        if ($is_public)
        {    
            $approval_message = $this->get_approval_message();
            if ($approval_message)
            {
                SessionMessages::add($approval_message);
            }
            
            $vars['messages'] = $this->get_messages($vars);            
            $vars['header'] = '';
        }
        
        parent::prepare_page_draw_vars($vars);        
        
        if ($is_public)
        {            
            $vars['header'] = view('page_elements/site_header', $vars);
        }
    }	
            
    public function not_found()
    {
        $uri_part = $this->param('user_uri');
        $user = $this->get_user();
        $redirect_url = NotFoundRedirect::get_redirect_url($uri_part, $user);
        if ($redirect_url)
        {
            $this->redirect($user->get_url() . $redirect_url);
        }
        else
        {
            parent::not_found();
        }
    }
}