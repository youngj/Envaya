<div id="topbar">
<table id='topbarTable'>
<tr>
<td id='logoContainer'>    
    <a href="<?php echo ((isloggedin()) ? 'pg/dashboard' : 'pg/home') ?>">
        <img src="_graphics/logo.gif" alt="Envaya" width="170" height="40">
    </a>
</td>
<td id='topbarLinks'>

<?php
     if (isloggedin()) {
?>

        <a href="<?php echo get_loggedin_user()->getURL() ?>" class='pagelinks'><?php echo elgg_echo('org:yours'); ?></a>

        <a href="pg/settings/" class="usersettings"><?php echo elgg_echo('settings'); ?></a>

        <?php

            // The administration link is for admin or site admin users only
            if ($vars['user']->admin) {

        ?>

            <a href="pg/admin/" class="usersettings"><?php echo elgg_echo("admin"); ?></a>

        <?php

                }

        ?>

<?php
    }
?>
    <?php    
        if (isloggedin())
        {
            echo "<a href='action/logout'>".elgg_echo("logout")."</a>";
        }
        else
        {
            echo "<a href='pg/login'>".elgg_echo("login")."</a>";
        }        
    ?>


</td>
<td style='text-align:right'>

    <?php    
        echo elgg_view("page_elements/select_language"); 
    ?>

</td>
</tr>
</table>

</div>

<div class="clearfloat"></div>