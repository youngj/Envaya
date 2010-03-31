<?php
    $org = $vars['org'];

    $form = elgg_view('org/addPost', array('org' => $org));
 
    echo elgg_view_layout('section', elgg_echo("dashboard:add_update"), $form);    

    $widgets = $org->getAvailableWidgets();

    $widgetList = array();
    
    foreach ($widgets as $widget)
    {
        $class = (!$widget->guid) ? 'class="widget_disabled"' : ''; 
        $widgetList[] .= "<a $class href='{$widget->getEditURL()}?from=pg/dashboard'>".elgg_echo("widget:{$widget->widget_name}")."</a>";
    }

    $widgets = "<div id='edit_pages_menu'>".implode(' ', $widgetList)."</div>";

    echo elgg_view_layout('section', elgg_echo("dashboard:edit_widgets"), $widgets);    
    
    ob_start();
?>
<table style='width:100%'>
<tr>
<td>
    <a class='icon_link icon_help' href='org/help'><?php echo elgg_echo('help:title') ?></a>
    <a class='icon_link icon_home' href='<?php echo $org->getURL() ?>'><?php echo elgg_echo('dashboard:view_home') ?></a>
    <a class='icon_link icon_settings' href='pg/settings'><?php echo elgg_echo('dashboard:settings') ?></a>
    <a class='icon_link icon_logout' href='action/logout'><?php echo elgg_echo('logout') ?></a>
</td>
<td>
    <a class='icon_link icon_explore' href='org/browse'><?php echo elgg_echo("browse:title") ?></a>
    <a class='icon_link icon_search' href='org/search'><?php echo elgg_echo("search:title") ?></a>
    <a class='icon_link icon_feed' href='org/feed'><?php echo elgg_echo("feed:title") ?></a>
</td>
</tr>
</table>
<?php
    $links = ob_get_clean();
    
    echo elgg_view_layout('section', elgg_echo("dashboard:links"), $links);    