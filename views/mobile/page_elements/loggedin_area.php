<?php

$user = Session::get_loggedin_user();

$links = array();

if ($user->is_setup_complete())
{
    $links[] = "<a href='{$user->get_url()}'>".__('your_home')."</a>";

    if ($user instanceof Organization)
    {
        $links[] = "<a href='{$user->get_url()}/dashboard'>".__('edit_site')."</a>";
    }

    $links[] = "<a href='{$user->get_url()}/settings'>".__('settings')."</a>";
}

if ($user->admin)
{
    $links[] = "<a href='{$user->get_url()}/dashboard'>Admin</a>";
}
$links[] = "<a href='/pg/logout'>".__('logout')."</a>";

echo implode(" &middot; ", $links);

$submenuB = get_submenu_group('edit', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group');
if ($submenuB)
{
    echo "<div id='edit_submenu'>$submenuB</div>";
}
?>