<?php

    gatekeeper();
    action_gatekeeper();

    $orgId = get_input('org_guid');
    $org = get_entity($orgId);
    $name = get_input('name');
    
    if (empty($name)) 
    {
        register_error(elgg_echo("widget:team:name:missing"));
        forward_to_referrer();
    } 
    else if (!$org || !$org->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else 
    {       
        $teamMember = new TeamMember();
        $teamMember->owner_guid = get_loggedin_userid();
        $teamMember->container_guid = $orgId;
        $teamMember->name = $name;
        $teamMember->description = get_input('description');    
        
        $team = $org->getTeamMembers();
        if (sizeof($team))
        {
            $teamMember->list_order = $team[sizeof($team)-1]->list_order + 1;
        }
        
        $teamMember->save();    
        
        $teamMember->setImages(get_uploaded_files('image'));        

        system_message(elgg_echo("widget:team:add_success"));
            
        $widget = $org->getWidgetByName('team');
        
        if (!$widget->guid)
        {
            $widget->save();
        }
        
        forward($widget->getEditURL());
    }
        
?>
