<?php

$org = $vars['org'];

if (Permission_ChangeUserApproval::has_for_entity($org))
{
    if ($org->approval == 0)
    {
        echo view('input/post_link', array(
            'text' => __('approval:approve'),
            'confirm' => __('areyousure'),
            'href' => "/{$org->username}/set_approval?approval=1"
        ));
        echo " ";
        echo view('input/post_link', array(
            'text' => __('approval:reject'),
            'confirm' => __('areyousure'),
            'href' => "/{$org->username}/set_approval?approval=-1"
        ));
        echo " ";
    }
    else
    {
        echo view('input/post_link', array(
            'text' => ($org->approval > 0) ? __('approval:unapprove') : __('approval:unreject'),
            'confirm' => __('areyousure'),
            'href' => "/{$org->username}/set_approval?approval=0"
        ));
        echo " ";
    }

    if ($org->approval < 0)
    {
        echo view('input/post_link', array(
            'text' => __('approval:delete'),
            'confirm' => __('areyousure'),
            'href' => "{$org->get_admin_url()}/disable"
        ));
        echo " ";
    }
}

if (Permission_EditUserSite::has_for_entity($org))
{
    echo " ";
    echo "<a href='/{$org->username}/dashboard'>".__('edit_site')."</a>";
    echo " ";
    echo "<a href='/{$org->username}/design'>".__('design:edit')."</a>";
    echo " ";
}

if (Permission_ViewUserSettings::has_for_entity($org))
{
    echo "<a href='/{$org->username}/settings'>".__('settings')."</a>";
    echo " ";
}

if (Permission_UseAdminTools::has_for_entity($org))
{
    echo "<a href='/{$org->username}/domains'>".__('domains:edit')."</a>";
    echo " ";
    
    if ($org->email)
    {
        $url = EmailSubscription::get_all_settings_url($org->email);
        echo "<a href='{$url}'>Email Subscriptions</a>";
        echo " ";
    }
}