<?php

    global $CONFIG;   
    
    $user = get_loggedin_user();
    
    if ($user instanceof Organization)
    {
        echo elgg_view("org/editPost", array('container_guid' => $user->guid));
               
        $widgets = $user->getAvailableWidgets();
            
        $widgetList = array();
        foreach ($widgets as $widget)
        {
            $class = (!$widget->guid) ? 'class="widget_disabled"' : ''; // TODO: & enabled
            $widgetList[] .= "<a $class href='{$widget->getURL()}/edit'>".elgg_echo("widget:{$widget->widget_name}")."</a>";
        }
            
        echo elgg_view_layout('section', elgg_echo("widgets:edit"), implode('<br>', $widgetList));    
    }
    else
    {
        echo "You are a regular user!";
    }
        

?>