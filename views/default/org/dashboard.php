<?php
    $org = $vars['org'];

    $form = view('org/addPost', array('org' => $org));

    echo view_layout('section', __("dashboard:add_update"), $form);

    $widgets = $org->get_available_widgets();

    $widgetList = array();

    foreach ($widgets as $widget)
    {
        $class = (!$widget->guid) ? 'class="widget_disabled"' : '';
        $widgetList[] .= "<a $class href='{$widget->get_edit_url()}?from=pg/dashboard'><span>".
            escape($widget->get_title())."</span></a>";
    }

    $widgets = "<div id='edit_pages_menu'>".implode(' ', $widgetList)."</div>";

    echo view_layout('section', __("dashboard:edit_widgets"), $widgets);

    ob_start();
?>
<table style='width:100%'>
<tr>
<td>
    <a class='icon_link icon_home' href='<?php echo $org->get_url() ?>'><?php echo __('dashboard:view_home') ?></a>
    <div class='icon_separator'></div>
    <!--
    <a class='icon_link icon_photos' href='<?php echo $org->get_url() . "/addphotos" ?>?from=pg/dashboard'><?php echo __('addphotos:title') ?></a>
    <div class='icon_separator'></div>
    -->
    <a class='icon_link icon_design' href='<?php echo $org->get_url() . "/design" ?>?from=pg/dashboard'><?php echo __('design:edit') ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_settings' href='<?php echo $org->username ?>/settings'><?php echo __('dashboard:settings') ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_help' href='<?php echo $org->get_url() ?>/help'><?php echo __('help:title') ?></a>
</td>
<td>
    <a class='icon_link icon_explore' href='org/browse'><?php echo __("browse:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_search' href='org/search'><?php echo __("search:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_feed' href='org/feed'><?php echo __("feed:title") ?></a>
    <div class='icon_separator'></div>
    <a class='icon_link icon_logout' href='pg/logout'><?php echo __('logout') ?></a>
</td>
</tr>
</table>
<?php
    $links = ob_get_clean();

    echo view_layout('section', __("dashboard:links"), $links);