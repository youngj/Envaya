<?php
    gatekeeper();
	action_gatekeeper();
	
	global $CONFIG;
	
    $partnerGuid = (int)get_input('partner_guid');
    $partner = get_entity($partnerGuid);

    $loggedInOrg = get_loggedin_user();
    
    $partnership = $loggedInOrg->getPartnership($partner);
    $partnership->setSelfApproved(true);
    $partnership->save();

    $partnership2 = $partner->getPartnership($loggedInOrg);
    $partnership2->setPartnerApproved(true);
    $partnership2->save();
    
    $url = $partnership->getApproveUrl();
    
    notify_user($partnerGuid, $CONFIG->site_guid, sprintf(elgg_echo('email:requestPartnership:subject'), $loggedInOrg->name, $partner->name), sprintf(elgg_echo('email:requestPartnership:body'), $url), NULL, 'email');
    
    system_message(elgg_echo("partner:request_sent"));  
    
    forward($partner->getUrl());
?>