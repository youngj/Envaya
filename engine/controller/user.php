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
        
    function get_user()
    {
        return $this->param('user');
    }       
        
    private function get_approval_message()
    {
        $user = $this->get_user();    
        switch ($user->approval)
        {
            case User::AwaitingApproval:    return __('approval:waiting');
            case User::Rejected:            return __('approval:rejected');
            default:                        return null;
        }        
    }
   
    function index_widget($widget)
    {
        $user = $this->get_user();
    
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
        
        Permission_ViewUserSite::require_for_entity($widget);
                
        if (Permission_EditUserSite::has_for_entity($widget) && !Input::get_string("__preview"))
        {
            $top_menu = PageContext::get_submenu('top');
        
            $top_menu->add_link(__("widget:edit"), $widget->get_edit_url());
            
            $top_menu->add_link(__('edit_design'), $user->get_url(). "/design?from={$widget->get_url()}");
        }
        
        $user_actions_menu = PageContext::get_submenu('user_actions');
                
        $url = $user->get_url();

        if (Permission_ChangeUserApproval::has_for_entity($user))
        {
            if ($user->approval == 0)
            {
                $user_actions_menu->add_item(view('input/post_link', array(
                    'text' => __('approval:approve'),
                    'confirm' => __('areyousure'),
                    'href' => "$url/set_approval?approval=1"
                )));
                $user_actions_menu->add_item(view('input/post_link', array(
                    'text' => __('approval:reject'),
                    'confirm' => __('areyousure'),
                    'href' => "$url/set_approval?approval=-1"
                )));
            }
            else
            {
                $user_actions_menu->add_item(view('input/post_link', array(
                    'text' => ($user->approval > 0) ? __('approval:unapprove') : __('approval:unreject'),
                    'confirm' => __('areyousure'),
                    'href' => "$url/set_approval?approval=0"
                )));
            }

            if ($user->approval < 0)
            {
                $user_actions_menu->add_item(view('input/post_link', array(
                    'text' => __('approval:delete'),
                    'confirm' => __('areyousure'),
                    'href' => "{$user->get_admin_url()}/disable"
                )));
            }
        }

        if (!$user->equals(Session::get_logged_in_user()))
        {
            if (Permission_EditUserSite::has_for_entity($user))
            {
                $user_actions_menu->add_link(__('edit_site'), "$url/dashboard");
                $user_actions_menu->add_link(__('design:edit'), "$url/design");
            }

            if (Permission_ViewUserSettings::has_for_entity($user))
            {
                $user_actions_menu->add_link(__('settings'), "$url/settings");
            }
        }

        if (Permission_UseAdminTools::has_for_entity($user))
        {
            $user_actions_menu->add_link(__('domains:edit'), "$url/domains");
            
            if ($user->email)
            {
                $user_actions_menu->add_link("Email Subscriptions", EmailSubscription::get_all_settings_url($user->email));
            }
            
            $user_actions_menu->add_link('User Properties', $user->get_admin_url());
        }                         
        
        if (Permission_UseAdminTools::has_for_entity($widget))
        {
            $user_actions_menu->add_link(__('widget:options'), "{$widget->get_base_url()}/options");
        }
        
        $container = $widget->get_container_entity();
        $content = $container->render_child_view($widget, array('is_primary' => true));
        
        $res = Hook_ViewWidget::trigger(array(
            'user' => $user,
            'widget' => $widget,
            'user_actions_menu' => $user_actions_menu,
            'page_draw_args' => array(
                'content' => $content,
                'title' => $widget->get_title(),
            )
        ));        
        
        $this->page_draw($res['page_draw_args']); 
    }
           
    function use_public_layout($cur_widget = null)
    {
        $user = $this->get_user();                
                
        $this->public_layout = true;
                
        $preview = Input::get_string("__preview");
                
        if ($preview && Permission_EditUserSite::has_for_entity($user))
        {
            $design_settings = json_decode($preview, true);
            $logo_override = @$design_settings['logo'];
            
            $this->page_draw_vars['preview'] = true;
        }
        else
        {
            $design_settings = $user->get_design_settings();
            $logo_override = null;
            
            $this->page_draw_vars['preview'] = false;
        }
                
        $theme = ClassRegistry::get_class(@$design_settings['theme_id']) ?: Config::get('theme:default');
        
        $this->page_draw_vars['design'] = $design_settings;
        $this->page_draw_vars['theme'] = $theme;
        $this->page_draw_vars['site_name'] = $user->name;
        $this->page_draw_vars['site_username'] = $user->username;
        $this->page_draw_vars['site_approved'] = $user->is_approved();
        $this->page_draw_vars['site_url'] = $user->get_url();     
        $this->page_draw_vars['logo'] = view('account/icon', array(
            'user' => $user, 
            'icon_props' => $logo_override,
            'size' => 'medium'
        ));          
        $this->page_draw_vars['login_url'] = url_with_param($this->full_rewritten_url(), 'login', 1);
        
        if (Views::get_request_type() == 'default')
        {
            Views::set_current_type($theme::get_viewtype());         
        }
        
        $this->show_site_menu($cur_widget);
    }
    
    private function show_site_menu($cur_widget)
    {
        $user = $this->get_user();
        
        $widgets = $user->query_menu_widgets()
            ->columns('guid,container_guid,owner_guid,language,widget_name,subtype_id,handler_arg,title,status,thumbnail_url')
            ->filter();
        
        foreach ($widgets as $widget)
        {
            $is_selected = $cur_widget && ($cur_widget->guid === $widget->guid);
        
            PageContext::get_submenu()->add_link(
                $widget->get_title(), 
                $widget->get_url(),
                $is_selected
            );
        }        
    }

    function use_editor_layout()
    {
        $this->page_draw_vars['theme'] = 'Theme_Editor';
    }

    protected function get_messages($vars)
    {
        $vars['user'] = $this->get_user();        
        return view('messages/usersite', $vars);        
    }
                
    public function prepare_page_draw_vars(&$vars)
    {
        $user = $this->get_user();
        
        $is_public = $this->public_layout;        
        if ($is_public)
        {    
            $approval_message = $this->get_approval_message();
            if ($approval_message)
            {
                SessionMessages::add($approval_message);
            }
            
            if (Input::get_string('__preview'))
            {
                $vars['messages'] = '';
            }
            else
            {
                $vars['messages'] = $this->get_messages($vars);            
            }
            $vars['header'] = '';
        }
        
        parent::prepare_page_draw_vars($vars);        
        
        if ($is_public)
        {            
            $vars['header'] = view('page_elements/site_header', $vars);
        }
    }	
            
    public function not_found($ex)
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
            parent::not_found($ex);
        }
    }   
}
