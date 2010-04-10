<?php
    admin_gatekeeper();
	action_gatekeeper();
	
	$guid = (int)get_input('org_guid');
	$entity = get_entity($guid);
	
	if (($entity) && ($entity instanceof Organization))
	{
        $entity->approval = (int)get_input('approval');        
        $entity->save();    
	    system_message(elgg_echo('approval:changed'));	
	}
	else
    {
		register_error(elgg_echo('approval:notapproved'));
    }    
		
	forward($entity->getUrl());
?>