<?php
	
	admin_gatekeeper();
    action_gatekeeper();
	
	$guid = get_input('guid');
	
	$entity = get_entity($guid);
	
	if (($entity) && ($entity->canEdit()))
	{
		if ($entity->delete())
			system_message(sprintf(elgg_echo('entity:delete:success'), $guid));
		else
			register_error(sprintf(elgg_echo('entity:delete:fail'), $guid));
	}
	else
		register_error(sprintf(elgg_echo('entity:delete:fail'), $guid));
		
    forward('pg/admin/user');
?>