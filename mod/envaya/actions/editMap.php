<?php
	/**
	 * Elgg Envaya plugin edit org map action.
	 * 
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */
	
	action_gatekeeper();
	        
	$org_guid = get_input('org_guid');   	
	$org = get_entity($org_guid);
    
	if (!$org || !$org->canEdit())
	{
		register_error(elgg_echo("org:cantedit"));
		
		forward($_SERVER['HTTP_REFERER']);
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
	exit;
?>