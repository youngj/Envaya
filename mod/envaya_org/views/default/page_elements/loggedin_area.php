<script type='text/javascript'>
function logoutClicked(e)
{
    var e = e || window.event;
    if (e.preventDefault) e.preventDefault();
    else e.returnValue = false;
    var div = $('logoutDiv');    
    div.style.display = (div.style.display == 'block') ? 'none' : 'block';
}
</script>
<?php

echo "<div id='loggedinArea'><span class='loggedInAreaContent'>";

$user = Session::get_logged_in_user();
if ($user->is_setup_complete())
{
    $url = $user->get_url();

    echo "<a href='{$url}' title=\"".__('your_home')."\"><img width='23' height='23' src='/_media/images/home2.gif' /></a>";       
    
    if (Permission_EditUserSite::has_for_entity($user))
    {
        echo "<a href='{$url}/dashboard' title=\"".__('edit_site')."\"><img width='21' height='20' src='/_media/images/pencil.gif?v3' /></a>";
        echo "<a href='{$url}/settings' title=\"".__('settings')."\" id='usersettings'><img  width='25' height='25' src='/_media/images/settings.gif' /></a>";
    }
    else
    {
        echo "<a href='{$url}/dashboard' title=\"".__('user:self_dashboard')."\"><img  width='25' height='25' src='/_media/images/settings.gif' /></a>";
    }    
}

echo "<a href='/pg/logout' onclick='logoutClicked(event)' title=\"".__('logout')."\"><img src='/_media/images/logout.gif' width='22' height='25' /></a>";
echo "</span>";

echo "<div id='logoutDiv'>";
echo view('input/post_link', array(
        'href' => '/pg/logout',
        'text' => __('logout'),
    ));
echo "</div>";

$submenuB = implode(' ', PageContext::get_submenu('edit')->get_items());
if ($submenuB)
{
    echo "<div id='edit_submenu'>$submenuB</div>";
}

echo "</div>";