<?php

    gatekeeper();
    action_gatekeeper();

    $org_guid = get_input('org_guid');
    $org = get_entity($org_guid);
    if ($org && $org->canEdit())
    {
        $name = get_input('widget_name');
        $widget = $org->getWidgetByName($name);        

        if (get_input('delete'))
        {
            $widget->disable('', $recursive=false);     
            
            system_message(elgg_echo('widget:delete:success'));            
            forward($org->getURL());
        }
        else
        {        
            $widget->saveInput();
            
            if (!$widget->isEnabled())
            {
                $widget->enable();
            }            
            
            system_message(elgg_echo('widget:save:success'));
            forward($widget->getURL());
        }
                
        
    }
    register_error(elgg_echo("org:cantedit"));      
    forward();
?>