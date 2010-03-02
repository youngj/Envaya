<?php

    gatekeeper();
	action_gatekeeper();
	        
	$org_guid = get_input('org_guid');   	
	$org = get_entity($org_guid);
    
	if (!$org || !$org->canEdit())
	{
		register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
		exit;
	}
	
    $org_lat = get_input('org_lat');
    $org_lng = get_input('org_lng');
    
    if($org_lat && $org_lng)
    {
        $org->setLatLong($org_lat, $org_lng);
        if ($org->save())
        {
            system_message(elgg_echo("org:mapSaved"));
            forward($org->getUrl());
        	exit;
        }        
    }

    register_error(elgg_echo("org:mapSaveError"));   
	forward($org->getUrl());
?>