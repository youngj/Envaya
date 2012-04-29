<?php

class Handler_EnvayaViewWidget
{
    static function execute($vars) {
    
        $user = $vars['user'];
        
        $logged_in_user = Session::get_logged_in_user();    
   
        if (($user instanceof Organization) && ($logged_in_user instanceof Organization) && !$user->equals($logged_in_user))
        {                    
            $top_menu = PageContext::get_submenu('top');
            if ($user->email)
            {
                $top_menu->add_link(__('message:link'), "{$user->get_url()}/send_message");
            }
            
            if (Relationship::query_for_user($logged_in_user)->where('subject_guid = ?', $user->guid)->is_empty())
            {        
                $networkPage = Widget_Network::get_or_new_for_entity($logged_in_user);            
                $top_menu->add_item(view('widgets/network_add_relationship_link', array(
                    'widget' => $networkPage, 
                    'org' => $user, 
                    'type' => Relationship::Partnership
                )));
            }
        }    
    
        $vars['page_draw_args']['show_next_steps'] = $vars['user']->equals(Session::get_logged_in_user());
        
        if (($user instanceof Organization) && Permission_EditMainSite::has_for_root())
        {
            $user_actions_menu = $vars['user_actions_menu'];
        
            $user_actions_menu->add_link(
                __('featured:add'),
                "/admin/envaya/add_featured?username={$user->username}"
            );
            
            $user_actions_menu->add_item(view('admin/featured_photo_link', array('user' => $user)));
        }
        
        if (Permission_EditMainSite::has_for_root())
        {        
            $widget = $vars['widget'];
            
            $user_actions_menu->add_link('Show on Home Page',
                "/admin/envaya/home_page?bottom_left_guid={$widget->guid}"
            );
        }                    
        
        return $vars;
    }
}