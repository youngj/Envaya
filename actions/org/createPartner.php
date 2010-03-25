<?php
    gatekeeper();
	action_gatekeeper();
	
	$requestedGuid = (int)get_input('requestedOrg_guid');
	$requestingGuid = (int)get_input('requestingOrg_guid');

	$entity = get_entity($requestingGuid);
	

    $pInfo = Partnership::getPartnership($requestedGuid,$requestingGuid);
    
    Partnership::approvePartnershipMember($org_guid=$requestedGuid, $partnership_id=$pInfo->partnership_id);
    
    $partWidget = $entity->getWidgetByName('partnerships');
    $partWidget->save();
    
    $partWidget = get_entity($requestedGuid)->getWidgetByName('partnerships');
    $partWidget->save();
    
    system_message(elgg_echo("org:partnershipCreated"));  
	forward($entity->getUrl());
?>
