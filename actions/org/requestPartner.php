<?php
    gatekeeper();
	action_gatekeeper();
	
	global $CONFIG;
	
    $requestedGuid = (int)get_input('org_guid');
    $entity = get_entity($requestedGuid);

    $loggedInOrg = get_loggedin_user();
    $requestingGuid = $loggedInOrg->guid;


    $partnership = new Partnership();
    $partnership->description = '';
    
    $partnership->save();
    $partnership->addPartnershipMember($requestedGuid);
    $partnership->addPartnershipMember($requestingGuid, 1);

    $url = Partnership::generatePartnerApproveUrl($requestedOrg_guid, $requestingOrg_guid);
    
    notify_user($guid, $CONFIG->site_guid, sprintf(elgg_echo('email:requestPartnership:subject'), $loggedInOrg->name, $entity->name), sprintf(elgg_echo('email:requestPartnership:body'), $url), NULL, 'email');
    
    system_message(elgg_echo("org:partnerRequestEmailed"));  
    //system_message($url);
    
    forward($entity->getUrl());
?>