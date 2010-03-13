<?php

    global $CONFIG;   
    
    $user = get_loggedin_user();
    
    if ($user instanceof Organization)
    {
        $form_body = '';
    
        $form_body .= elgg_view('input/longtext', array('internalname' => 'blogbody', 'js' => 'style="height:100px"', 'value' => $body));
        
        $form_body .= elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $user->guid));
        
        $form_body .= "<div style='text-align:right'>".elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('update_status')))."</div>";

        echo elgg_view('input/form', 
            array('action' => "{$vars['url']}action/news/add", 'body' => $form_body, 'internalid' => 'blogPostForm'));
            
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