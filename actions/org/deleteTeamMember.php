<?php

    gatekeeper();
    action_gatekeeper();

    $memberId = get_input('member');
    $member = get_entity($memberId);
    
    if (!$member || !$member->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else 
    {   
        $org = $member->getContainerEntity();
        $member->delete();
        
        system_message(elgg_echo('widget:team:delete_success'));
        
        $widget = $org->getWidgetByName('team');
        forward($widget->getEditURL());
    }
        
?>
