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
<div class='dashboard_links'>    
    <div>
        <a class='dashboard_img_link' href='org/help'><img src='_graphics/help.gif' /></a>
        <a class='dashboard_text_link' href='org/help'><?php echo elgg_echo('help:title') ?></a>
    </div>        
    <div>
        <a class='dashboard_img_link' href='<?php echo $org->getURL() ?>'><img src='_graphics/home.gif?v2' /></a>
        <a class='dashboard_text_link' href='<?php echo $org->getURL() ?>'><?php echo elgg_echo('dashboard:view_home') ?></a>
    </div>    
    <div>
        <a class='dashboard_img_link' href='pg/settings'><img src='_graphics/settings.gif' /></a>
        <a class='dashboard_text_link' href='pg/settings'><?php echo elgg_echo('dashboard:settings') ?></a>
    </div>    
    <div>
        <a class='dashboard_img_link' href='action/logout'><img src='_graphics/logout.gif' /></a>
        <a class='dashboard_text_link' href='action/logout'><?php echo elgg_echo('logout') ?></a>
    </div>    
</div>    
</td>
<td>
<div class='dashboard_links'>
    <div>
        <a class='dashboard_img_link_r' href='org/browse'><img src='_graphics/globe.gif' /></a>
        <a class='dashboard_text_link' href='org/browse'><?php echo elgg_echo("browse:title") ?></a>
    </div>
    <div>
        <a class='dashboard_img_link_r' href='org/search'><img src='_graphics/search.gif' /></a>
        <a class='dashboard_text_link' href='org/search'><?php echo elgg_echo("search:title") ?></a>
    </div>
    <div>
        <a class='dashboard_img_link_r' href='org/feed'><img src='_graphics/icons/default/small.png' /></a>
        <a class='dashboard_text_link' href='org/feed'><?php echo elgg_echo("feed:title") ?></a>
    </div>
</div>
</td>
</tr>
</table>
<?php
    $links = ob_get_clean();
    
    echo elgg_view_layout('section', elgg_echo("dashboard:links"), $links);    