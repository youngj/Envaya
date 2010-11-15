<?php

$user = Session::get_loggedin_user();

$links = array();

if ($user->is_setup_complete())
{
    $links[] = "<a href='{$user->get_url()}'>".__('your_home')."</a>";

    if ($user instanceof Organization)
    {
        $links[] = "<a href='{$user->get_url()}/dashboard?t=".time()."'>".__('edit_site')."</a>";
    }

    $links[] = "<a href='{$user->get_url()}/settings'>".__('settings')."</a>";
}

if ($user->admin)
{
    $links[] = "<a href='{$user->get_url()}/dashboard'>Admin</a>";
}
$links[] = "<a href='/pg/logout'>".__('logout')."</a>";

echo implode(" &nbsp; ", $links);

?>