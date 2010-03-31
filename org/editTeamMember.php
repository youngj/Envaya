<?php
    gatekeeper();
        
    $member_guid = (int) get_input('member_guid');
    $member = get_entity($member_guid);
    
    set_theme('editor');
    set_context('editor');
    
    if ($member && $member->canEdit()) 
    {                   
        $title = elgg_echo('widget:team:edit');
        $org = $member->getContainerEntity();
        $teamWidget = $org->getWidgetByName('team');
    
        $cancelUrl = get_input('from') ?: $teamWidget->getEditURL();

        add_submenu_item(elgg_echo("canceledit"), $cancelUrl, 'edit');                
    
        $area1 = elgg_view("org/editTeamMember", array('entity' => $member));
        $body = elgg_view_layout("one_column_padded", elgg_view_title($title), $area1);        
        
        page_draw($title,$body);      
    }
    else 
    {
        not_found();
    }
        

?>