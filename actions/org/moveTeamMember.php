<?php

    gatekeeper();
    action_gatekeeper();

    $memberId = (int)get_input('member');
    $member = get_entity($memberId);
    $delta = (int)get_input('delta');
    
    if (!$member || !$member->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else 
    {   
        $org = $member->getContainerEntity();        
        
        $team = $org->getTeamMembers();
        $index = get_guid_index($team, $memberId);        

        if (array_move_item($team, $index, $delta))
        {
            $order = 0;
            foreach ($team as $teamMember)
            {
                $teamMember->list_order = $order;
                $order = $order + 1;
                $teamMember->save();
            }
            
            system_message(elgg_echo('widget:team:move_success'));
        }        
        
        $widget = $org->getWidgetByName('team');
                
        
        forward($widget->getEditURL());
    }
        
?>
