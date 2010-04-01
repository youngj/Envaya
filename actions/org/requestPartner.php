<?php
    gatekeeper();
	action_gatekeeper();
	
	global $CONFIG;
	
    $partner_guid = (int)get_input('partner_guid');
    $partner = get_entity($partner_guid);

    $loggedInOrg = get_loggedin_user();
    
    if (!$partner || $partner_guid == $loggedInOrg->guid)
    {
        register_error(elgg_echo("partner:invalid"));   
        forward();
    }
    else 
    {
        $partnership = $loggedInOrg->getPartnership($partner);
        $partnership->setSelfApproved(true);
        $partnership->save();

        $partnership2 = $partner->getPartnership($loggedInOrg);
        $partnership2->setPartnerApproved(true);
        $partnership2->save();

        notify_user($partner_guid, $CONFIG->site_guid, 
            sprintf(elgg_echo('email:requestPartnership:subject',$partner->language), $loggedInOrg->name, $partner->name), 
            sprintf(elgg_echo('email:requestPartnership:body',$partner->language), $partnership->getApproveUrl()),
            NULL, 'email');

        system_message(elgg_echo("partner:request_sent"));  

        forward($partner->getUrl());
    }    
?>