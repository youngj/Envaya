<?php

    gatekeeper();
    action_gatekeeper();

    $org_guid = get_input('org_guid');
    $org = get_entity($org_guid);
    if ($org && $org->canEdit())
    {
        $name = get_input('widget_name');
        $widget = $org->getWidgetByName($name);
        $widget->saveInput();
        system_message(elgg_echo('widgets:save:success'));
        forward($widget->getURL());
    }
    register_error(elgg_echo("org:cantedit"));      
    forward();
?>