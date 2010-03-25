<?php

	/**
	 * Elgg add comment action
	 * 
	 * @package Elgg

	 * @author Curverider <curverider.co.uk>

	 * @link http://elgg.org/
	 */

	// Make sure we're logged in; forward to the front page if not
		gatekeeper();
		action_gatekeeper();
		
	// Get input
		$entity_guid = (int) get_input('entity_guid');
		$comment_text = get_input('generic_comment');
		
	// Let's see if we can get an entity with the specified GUID
		if ($entity = get_entity($entity_guid)) {
			
	        // If posting the comment was successful, say so
				if ($entity->annotate('generic_comment',$comment_text,$entity->access_id, $_SESSION['guid'])) {
					
					if ($entity->owner_guid != get_loggedin_userid())
					notify_user($entity->owner_guid, get_loggedin_userid(), elgg_echo('generic_comment:email:subject'), 
						sprintf(
									elgg_echo('generic_comment:email:body'),
									$entity->title,
									get_loggedin_user()->name,
									$comment_text,
									$entity->getURL(),
									get_loggedin_user()->name,
									get_loggedin_user()->getURL()
								)
					); 
					
					system_message(elgg_echo("generic_comment:posted"));
					
				} else {
					register_error(elgg_echo("generic_comment:failure"));
				}
				
		} else {
		
			register_error(elgg_echo("generic_comment:notfound"));
			
		}
		
	// Forward to the 
		forward($entity->getURL());

?>