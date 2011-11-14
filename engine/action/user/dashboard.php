<?php

class Action_User_Dashboard extends Action
{
    function render()
    {
        $user = $this->get_user();
    
        Permission_ViewUserDashboard::require_for_entity($user);
    
        $this->use_editor_layout();        
        $this->allow_view_types(null);        
        
        $vars = array();

        $is_self_user = $user->equals(Session::get_logged_in_user());
        
        if (Permission_EditUserSite::has_for_entity($user))
        {
            $vars['title'] = $is_self_user ? __('edit_site') : sprintf(__('edit_item'), $user->name);
        }
        else
        {
            $vars['title'] = $is_self_user ? __('user:self_dashboard') : sprintf(__('user:other_dashboard'), $user->name);
        }
        
        $links = PageContext::get_submenu('dashboard_links');                
        $url = $user->get_url();
    
        $add_link = function($href, $class, $text) use ($links)
        {
            $links->add_item(view('account/link_item', array('href' => $href, 'class' => $class, 'text' => $text)));
        };

        if ($is_self_user)
        {
            if (Permission_UseAdminTools::has_for_root())
            {    
                $add_link('/admin/statistics', 'icon_admin', 'Statistics');
                $add_link('/admin/logbrowser', 'icon_admin', 'Log Browser');
                $add_link('/admin/outgoing_mail', 'icon_admin', 'Outgoing Mail');
                $add_link('/admin/outgoing_sms', 'icon_admin', 'Outgoing SMS');
                $add_link('/admin/recent_photos', 'icon_photos', 'Recent Photos');
                $add_link('/admin/recent_documents', 'icon_admin', 'Recent Documents');
                $add_link('/admin/entities', 'icon_admin', 'Manage Entities');
                $add_link('/admin/subscriptions', 'icon_admin', 'Manage Subscriptions');
            }
        }        
        
        $add_link($url, 'icon_home', __('dashboard:view_home'));
        
        if (Permission_EditUserSite::has_for_entity($user))
        {
            $add_link("{$url}/design?from=/pg/dashboard", 'icon_design', __('design:edit'));
            $add_link("{$url}/addphotos?from=/pg/dashboard&t=".timestamp(), 'icon_photos', __('upload:photos:title'));
        }    
        
        Hook_ViewDashboard::trigger(array(
            'user' => $user,
            'dashboard_links_menu' => $links,
        ));

        $vars['content'] = view("account/dashboard", array('user' => $user));
        $vars['messages'] = view('messages/dashboard', array('user' => $user));
        
        $this->page_draw($vars);
    }
}