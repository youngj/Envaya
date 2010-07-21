<?php
    $org = $vars['org'];

    $form = elgg_view('org/addPost', array('org' => $org));

    echo elgg_view_layout('section', elgg_echo("dashboard:add_update"), $form);

    $widgets = $org->getAvailableWidgets();

    $widgetList = array();

    foreach ($widgets as $widget)
    {
        $class = (!$widget->guid) ? 'class="widget_disabled"' : '';
        $widgetList[] .= "<a $class href='{$widget->getEditURL()}?from=pg/dashboard'><span>".elgg_echo("widget:{$widget->widget_name}")."</span></a>";
    }

    $widgets = "<div id='edit_pages_menu'>".implode(' ', $widgetList)."</div>";

    echo elgg_view_layout('section', elgg_echo("dashboard:edit_widgets"), $widgets);

    ob_start();
?>
<table style='width:100%'>
<tr>
<td>
    <a class='icon_link icon_home' href='<?php echo $org->getURL() ?>'><?php echo elgg_echo('dashboard:view_home') ?></a>
    <div class='icon_separator'></div>
    <!--
    <a class='icon_link icon_photos' href='<?php echo $org->getURL() . "/addphotos" ?>?from=pg/dashboard'><?php echo elgg_echo('addphotos:title') ?></a>
    <div class='icon_separator'></div>
    -->
    <a class='icon_link icon_design' href='<?php echo $org->getURL() . "/design" ?>?from=pg/dashboard'><?php echo elgg_echo('design:edit') ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_settings' href='<?php echo $org->username ?>/settings'><?php echo elgg_echo('dashboard:settings') ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_help' href='<?php echo $org->getURL() ?>/help'><?php echo elgg_echo('help:title') ?></a>
</td>
<td>
    <a class='icon_link icon_explore' href='org/browse'><?php echo elgg_echo("browse:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_search' href='org/search'><?php echo elgg_echo("search:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_feed' href='org/feed'><?php echo elgg_echo("feed:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_logout' href='action/logout'><?php echo elgg_echo('logout') ?></a>
</td>
</tr>
</table>
<?php
    $links = ob_get_clean();

    echo elgg_view_layout('section', elgg_echo("dashboard:links"), $links);