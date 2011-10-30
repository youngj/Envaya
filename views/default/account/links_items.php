<?php
    $user = $vars['user'];       
    $url = $user->get_url();
    
    $make_link = function($href, $class, $text)
    {
        return view('account/link_item', array('href' => $href, 'class' => $class, 'text' => $text))
            ."<div class='icon_separator'></div>";
    };

    if ($user->equals(Session::get_logged_in_user()) && Permission_UseAdminTools::has_for_root())
    {
        echo $make_link('/admin/statistics', 'icon_admin', 'Statistics');
        echo $make_link('/admin/logbrowser', 'icon_admin', 'Log Browser');
        echo $make_link('/admin/outgoing_mail', 'icon_admin', 'Outgoing Mail');
        echo $make_link('/admin/outgoing_sms', 'icon_admin', 'Outgoing SMS');
        echo $make_link('/admin/recent_photos', 'icon_admin', 'Recent Photos');
        echo $make_link('/admin/recent_documents', 'icon_admin', 'Recent Documents');
        echo $make_link('/admin/entities', 'icon_admin', 'Manage Entities');
        echo $make_link('/admin/subscriptions', 'icon_admin', 'Manage Subscriptions');
    }
    
    echo $make_link($url, 'icon_home', __('dashboard:view_home'));
    
    if (Permission_EditUserSite::has_for_entity($user))
    {
        echo $make_link("{$url}/design?from=/pg/dashboard", 'icon_design', __('design:edit'));
        echo $make_link("{$url}/addphotos?from=/pg/dashboard&t=".timestamp(), 'icon_photos', __('upload:photos:title'));
    }    
    if (Permission_ViewUserSettings::has_for_entity($user))
    {
        echo $make_link("{$url}/settings", 'icon_settings', __('dashboard:settings'));
    }            