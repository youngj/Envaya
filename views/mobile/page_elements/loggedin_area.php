<?php

$user = Session::get_logged_in_user();
$links = array();

if ($user->is_setup_complete())
{
    if ($user instanceof Organization)
    {
        $links[] = "<a href='{$user->get_url()}'>".__('your_home')."</a>";
    }

    $links[] = "<a href='{$user->get_url()}/dashboard?t=".timestamp()."'>".__('edit_site')."</a>";
    $links[] = "<a href='{$user->get_url()}/settings'>".__('settings')."</a>";
}

$links[] = "<a href='/pg/logout'>".__('logout')."</a>";

echo implode(" &nbsp; ", $links);
