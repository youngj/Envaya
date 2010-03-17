<?php
    $org = $vars['org'];

    $form = elgg_view('org/addPost', array('org' => $org));
 
    echo elgg_view_layout('section', elgg_echo("dashboard:add_update"), $form);    

    $widgets = $org->getAvailableWidgets();

    $widgetList = array();
    
    foreach ($widgets as $widget)
    {
        $class = (!$widget->guid) ? 'class="widget_disabled"' : ''; // TODO: & enabled
        $widgetList[] .= "<a $class href='{$widget->getEditURL()}?from=pg/dashboard'>".elgg_echo("widget:{$widget->widget_name}")."</a>";
    }

    echo elgg_view_layout('section', elgg_echo("dashboard:edit_widgets"), implode('<br>', $widgetList));    