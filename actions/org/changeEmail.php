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
	
    $org->generateEmailCode();    	
    
    system_message(elgg_echo('widget:news:postemailchanged'));
	forward_to_referrer();
?>