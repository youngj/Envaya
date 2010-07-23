<?php

if (get_input('__topbar') !== '0') {

?>

<div id="topbar">
<table class='topbarTable'>
<td class='topbarLinks'>
    <a id='logoContainer' href="<?php echo ((isloggedin()) ? 'pg/dashboard' : 'home') ?>">
        <img src="_graphics/logo.gif?v5" alt="Envaya" width="145" height="30">
    </a>
    <a href='org/browse'><?php echo __('browse') ?></a>
    <a href='org/search'><?php echo __('search') ?></a>
    <a href='org/feed'><?php echo __('feed') ?></a>
</td>
<td width='166'>&nbsp;</td>
</tr>
</table>

<?php if (get_context() != "login") { ?>
<div id='topRight'>

    <?php

        if (isloggedin())
        {
            echo "<div id='loggedinArea'><span class='loggedInAreaContent'>";

            $user = get_loggedin_user();

            if ($user->isSetupComplete())
            {
                echo "<a href='{$user->getURL()}' title=\"".__('topbar:your_home')."\"><img src='_graphics/home.gif?v2' /></a>";

                if ($user instanceof Organization)
                {
                    echo "<a href='pg/dashboard' title=\"".__('topbar:edit_site')."\"><img src='_graphics/pencil.gif?v3' /></a>";
                }

                echo "<a href='{$user->getURL()}/settings' title=\"".__('settings')."\" id='usersettings'><img src='_graphics/settings.gif' /></a>";
            }

            // The administration link is for admin or site admin users only
            if ($vars['user']->admin)
            {
                echo "<a href='pg/dashboard'><img src='_graphics/admin.gif' height='25' width='24' /></a>";
            }

            echo "<a href='pg/logout' title=\"".__('logout')."\"><img src='_graphics/logout.gif' /></a>";

            echo "</span>";

            $submenuB = get_submenu_group('edit', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group');
            if ($submenuB)
            {
                echo "<div id='edit_submenu'>$submenuB</div>";
            }

            echo "</div>";
        }
        else
        {
            $loginUrl = (get_context() == 'orgprofile') ? url_with_param(Request::instance()->full_rewritten_url(), 'login',1) : 'pg/login';

            echo "<a id='loginButton' href='".escape($loginUrl)."'><span class='loginContent'><img src='_graphics/lock.gif' height='20' width='20' /><span>".__("login")."</span></span></a>";
        }

    ?>

</div>

<?php } ?>

</div>

<div class="clearfloat"></div>

<?php

}

?>
