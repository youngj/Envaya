<div id="topbar">
<table id='topbarTable'>
<tr>
<td id='logoContainer'>    
    <a href="<?php echo ((isloggedin()) ? 'pg/dashboard' : 'pg/home') ?>">
        <img src="_graphics/logo.gif" alt="Envaya" width="170" height="40">
    </a>
</td>
<td class='topbarLinks'>

<?php
     if (isloggedin() && get_loggedin_user()->isSetupComplete()) {
?>

        <a href="<?php echo get_loggedin_user()->getURL() ?>" class='pagelinks'><?php echo elgg_echo('org:yours'); ?></a>


        <?php

            // The administration link is for admin or site admin users only
            if ($vars['user']->admin) {

        ?>

            <a href="pg/admin/" class="usersettings"><?php echo elgg_echo("admin"); ?></a>

        <?php

                }
        
        echo get_submenu_group('b', 'canvas_header/topbar_submenu', 'canvas_header/topbar_submenu_group');

        ?>


<?php
    }
?>


</td>
<td class='topbarLinks' style='text-align:right'>

    <?php    
        if (isloggedin())
        {
            if (get_loggedin_user()->isSetupComplete())
            {
                echo '<a href="pg/settings/" class="usersettings">'.elgg_echo('settings').'</a>';
            }
        
            echo "<a href='action/logout'>".elgg_echo("logout")."</a>";
        }
        else
        {
            echo "<a href='pg/login'>".elgg_echo("login")."</a>";
        }        
    ?>
    
    <?php            
    
        echo elgg_view("page_elements/select_language"); 
    ?>

</td>
</tr>
</table>

</div>

<div class="clearfloat"></div>