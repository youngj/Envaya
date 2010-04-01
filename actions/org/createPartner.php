<?php
    gatekeeper();
    action_gatekeeper();
	
    $user = get_loggedin_user();
    $partner_guid = (int)get_input("partner_guid");
        
    $partner = get_entity($partner_guid);

    if (!$partner || $partner_guid == $user->guid)
    {
        register_error(elgg_echo("partner:invalid"));   
        forward();
    }
    else 
    {

        $partnership = $partner->getPartnership($user);
        $partnership->setPartnerApproved(true);
        $partnership->save();

        $partnership2 = $user->getPartnership($partner);
        $partnership2->setSelfApproved(true);
        $partnership2->save();

        $partWidget = $user->getWidgetByName('partnerships');
        $partWidget->save();

        $partWidget2 = $partner->getWidgetByName('partnerships');
        $partWidget2->save();

        system_message(elgg_echo("partner:created"));  
        
        notify_user($partner_guid, null,
            sprintf(elgg_echo('email:partnershipConfirmed:subject',$partner->language), $user->name, $partner->name), 
            sprintf(elgg_echo('email:partnershipConfirmed:body',$partner->language), $partWidget2->getURL()), 
            NULL, 'email');

        forward($partWidget->getURL());
    }