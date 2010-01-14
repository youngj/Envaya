<?php
	global $CONFIG;
	action_gatekeeper();
	
	$guid = (int)get_input('org_guid');
	$entity = get_entity($guid);
	
	if (($entity) && ($entity instanceof Organization))
	{
		if ($entity->verifyOrg())
		{
			$entity->save();
			system_message(elgg_echo('org:verified'));
		}
		else
			register_error(elgg_echo('org:notverified'));
	}
	else
		register_error(elgg_echo('org:notverified'));
		
	forward($entity->getUrl());
?>