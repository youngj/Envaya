<?php

class Handler_ContactViewDashboard
{
    static function execute($vars)
    {
        $user = $vars['user'];
        
        if ($user->equals(Session::get_logged_in_user()) && Permission_UseAdminTools::has_any())
        {
            $links = $vars['dashboard_links_menu'];
        
            $links->add_item(view('account/link_item', array(
                'href' => '/admin/contact', 
                'text' => __('contact:user_list'),
                'class' => 'icon_admin'
            )));
            
            $links->add_item(view('account/link_item', array(
                'href' => '/admin/contact/email', 
                'text' => sprintf(__('contact:template_list'), __('contact:email')),
                'class' => 'icon_admin'
            )));
            
            $links->add_item(view('account/link_item', array(
                'href' => '/admin/contact/sms', 
                'text' => sprintf(__('contact:template_list'), __('contact:sms')),
                'class' => 'icon_admin'
            )));
        }
    }
}
