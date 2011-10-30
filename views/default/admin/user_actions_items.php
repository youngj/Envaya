<?php

$user = $vars['user'];

$url = $user->get_url();

if (Permission_ChangeUserApproval::has_for_entity($user))
{
    if ($user->approval == 0)
    {
        echo view('input/post_link', array(
            'text' => __('approval:approve'),
            'confirm' => __('areyousure'),
            'href' => "$url/set_approval?approval=1"
        ));
        echo " ";
        echo view('input/post_link', array(
            'text' => __('approval:reject'),
            'confirm' => __('areyousure'),
            'href' => "$url/set_approval?approval=-1"
        ));
        echo " ";
    }
    else
    {
        echo view('input/post_link', array(
            'text' => ($user->approval > 0) ? __('approval:unapprove') : __('approval:unreject'),
            'confirm' => __('areyousure'),
            'href' => "$url/set_approval?approval=0"
        ));
        echo " ";
    }

    if ($user->approval < 0)
    {
        echo view('input/post_link', array(
            'text' => __('approval:delete'),
            'confirm' => __('areyousure'),
            'href' => "{$user->get_admin_url()}/disable"
        ));
        echo " ";
    }
}

if (Permission_EditUserSite::has_for_entity($user))
{
    echo " ";
    echo "<a href='$url/dashboard'>".__('edit_site')."</a>";
    echo " ";
    echo "<a href='$url/design'>".__('design:edit')."</a>";
    echo " ";
}

if (Permission_ViewUserSettings::has_for_entity($user))
{
    echo "<a href='$url/settings'>".__('settings')."</a>";
    echo " ";
}

if (Permission_UseAdminTools::has_for_entity($user))
{
    echo "<a href='$url/domains'>".__('domains:edit')."</a>";
    echo " ";
    
    if ($user->email)
    {
        $url = EmailSubscription::get_all_settings_url($user->email);
        echo "<a href='{$url}'>Email Subscriptions</a>";
        echo " ";
    }
}