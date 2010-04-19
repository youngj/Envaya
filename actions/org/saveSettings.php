<?php    
	global $CONFIG;
	
    gatekeeper();
	action_gatekeeper();
	
    $user_id = get_input('guid');    
    $org = get_entity($user_id);

    if ($org && $org instanceof Organization && $org->canEdit())
    {
    }    
	
?>