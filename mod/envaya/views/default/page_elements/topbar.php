<div id="topbar">
    <div id='logo_container'>
        <a href="<?php echo $vars['config']->wwwroot . ((isloggedin()) ? 'pg/dashboard' : '') ?>">
            <img src="<?php echo $vars['config']->wwwroot ?>mod/envaya/graphics/logo.gif" alt="Envaya" width="170" height="40">
        </a>
    </div>

<div id="topbar_container_left">

<?php
     if (isloggedin()) {
?>

        <a href="<?php echo get_loggedin_user()->getURL() ?>" class='pagelinks'><?php echo elgg_echo('org:yours'); ?></a>

        <a href="<?php echo $vars['url']; ?>pg/settings/" class="usersettings"><?php echo elgg_echo('settings'); ?></a>

        <?php

            // The administration link is for admin or site admin users only
            if ($vars['user']->admin) {

        ?>

            <a href="<?php echo $vars['url']; ?>pg/admin/" class="usersettings"><?php echo elgg_echo("admin"); ?></a>

        <?php

                }

        ?>

<?php
    }
?>
    <?php    
        if (isloggedin())
        {
            echo "<a href='".$CONFIG->wwwroot."action/logout'>".elgg_echo("logout")."</a>";
        }
        else
        {
            echo "<a href='".$CONFIG->wwwroot."pg/login'>".elgg_echo("login")."</a>";
        }        
    ?>

    <?php    
        echo elgg_view("page_elements/select_language"); 
    ?>


<form id="searchform" action="<?php echo $vars['url']; ?>pg/org/search/" method="get">
    <input type="text" size="21" name="q" value="<?php echo elgg_echo('search'); ?>" onclick="if (this.value=='<?php echo elgg_echo('search'); ?>') { this.value='' }" class="search_input" />
    <input type="submit" value="<?php echo elgg_echo('search:go'); ?>" class="search_submit_button" />
</form>


</div>

</div>

<div class="clearfloat"></div>