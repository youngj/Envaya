<?php
    admin_gatekeeper();
	action_gatekeeper();
	
	$guid = (int)get_input('org_guid');
	$entity = get_entity($guid);
	
	if (($entity) && ($entity instanceof Organization))
	{
        $approvedBefore = $entity->isApproved();         
        
        $entity->approval = (int)get_input('approval');        
        
        $approvedAfter = $entity->isApproved();
              
        $entity->save();    
        
        if (!$approvedBefore && $approvedAfter)
        {
            notify_user($entity->guid, $CONFIG->site_guid, 
                elgg_echo('email:orgapproved:subject', $entity->language), 
                sprintf(elgg_echo('email:orgapproved:body', $entity->language), 
                    $entity->name, 
                    $entity->getURL(), 
                    "{$CONFIG->url}pg/login", 
                    elgg_echo('help:title', $entity->language),
                    "{$CONFIG->url}org/help"
                ),
                NULL, 'email');
        }
        
	    system_message(elgg_echo('approval:changed'));	
	}
	else
    {
		register_error(elgg_echo('approval:notapproved'));
    }    
		
	forward($entity->getUrl());
?>