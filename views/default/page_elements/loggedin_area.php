<?php

echo "<div id='loggedinArea'><span class='loggedInAreaContent'>";

$user = Session::get_loggedin_user();

if ($user->is_setup_complete())
{
    echo "<a href='{$user->get_url()}' title=\"".__('your_home')."\"><img width='23' height='24' src='/_graphics/home.gif?v2' /></a>";

    if ($user instanceof Organization)
    {
        echo "<a href='{$user->get_url()}/dashboard' title=\"".__('edit_site')."\"><img width='21' height='20' src='/_graphics/pencil.gif?v3' /></a>";
    }

    echo "<a href='{$user->get_url()}/settings' title=\"".__('settings')."\" id='usersettings'><img  width='25' height='25' src='/_graphics/settings.gif' /></a>";
}

if ($user->admin)
{
    echo "<a href='{$user->get_url()}/dashboard'><img src='/_graphics/admin.gif' width='24' height='25' /></a>";
}

echo "<a href='/pg/logout' title=\"".__('logout')."\"><img src='/_graphics/logout.gif' width='22' height='25' /></a>";

echo "</span>";

$submenuB = PageContext::get_submenu_group('edit', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group');
if ($submenuB)
{
    echo "<div id='edit_submenu'>$submenuB</div>";
}

echo "</div>";