<?php

if (get_input('__topbar') !== '0') {

?>

<div id="topbar">
<table class='topbarTable'>
<td class='topbarLinks'>
    <a id='logoContainer' href="<?php echo ((isloggedin()) ? 'pg/dashboard' : 'pg/home') ?>">
        <img src="_graphics/logo.gif?v3" alt="Envaya" width="145" height="30">
    </a>
    <a href='org/browse'><?php echo elgg_echo('browse') ?></a>
    <a href='org/search'><?php echo elgg_echo('search') ?></a>
    <a href='org/feed'><?php echo elgg_echo('feed') ?></a>    
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
                echo "<a href='{$user->getURL()}' title=\"".elgg_echo('topbar:your_home')."\"><img src='_graphics/home.gif?v2' /></a>";
                
                if ($user instanceof Organization)
                {
                    echo "<a href='pg/dashboard' title=\"".elgg_echo('topbar:edit_site')."\"><img src='_graphics/pencil.gif?v3' /></a>";
                }    
                
                echo "<a href='pg/settings/' title=\"".elgg_echo('settings')."\" id='usersettings'><img src='_graphics/settings.gif' /></a>";                
            }            

            // The administration link is for admin or site admin users only
            if ($vars['user']->admin) 
            {
                echo "<a href='pg/admin/'><img src='_graphics/admin.gif' height='25' width='24' /></a>";                
            }                    
            
            echo "<a href='action/logout' title=\"".elgg_echo('logout')."\"><img src='_graphics/logout.gif' /></a>";
            
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
            echo "<a id='loginButton' href='pg/login'><span class='loginContent'><img src='_graphics/lock.gif' height='20' width='20' /><span>".elgg_echo("login")."</span></span></a>";
        }   
           
    ?>    
    
</div>

<?php } ?>

</div>

<div class="clearfloat"></div>

<?php

}

?>
