<?php
    gatekeeper();
	action_gatekeeper();
	
    $user = get_loggedin_user();
    $partner_guid = (int)get_input("partner_guid");
    
    $partner = get_entity($partner_guid);
    
    $partnership = $partner->getPartnership($user);
    $partnership->setPartnerApproved(true);
    $partnership->save();
    
    $partnership2 = $user->getPartnership($partner);
    $partnership2->setSelfApproved(true);
    $partnership2->save();
    
    $partWidget = $user->getWidgetByName('partnerships');
    $partWidget->save();
    
    $partWidget = $partner->getWidgetByName('partnerships');
    $partWidget->save();
    
    system_message(elgg_echo("partner:created"));  
    
	forward($partner->getUrl());
