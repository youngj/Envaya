<?php

$links = array();

$user = Session::get_logged_in_user();
if ($user->is_setup_complete())
{
    $url = $user->get_url();
    
    $links[] = "<a href='{$url}'>".__('your_home')."</a>";       
    
    if (Permission_EditUserSite::has_for_entity($user))
    {
        $links[] = "<a href='{$url}/dashboard?t=".timestamp()."'>".__('edit_site')."</a>";
        $links[] = "<a href='{$url}/settings?t=".timestamp()."'>".__('settings')."</a>";
    }
    else
    {
        $links[] = "<a href='{$url}/dashboard?t='".timestamp()."'>".__('user:self_dashboard')."</a>";
    }        
}

$links[] = "<a href='/pg/logout'>".__('logout')."</a>";

echo implode(" &nbsp; ", $links);
