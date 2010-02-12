<?php
	/**
	 * Elgg Envaya plugin edit org action.
	 * 
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */
	
	action_gatekeeper();
	        
	$org_guid = get_input('org_guid');   
	
	$org = new Organization($org_guid); // load if present, if not create a new org
	if (!$org_guid || !$org->canEdit())
	{
		register_error(elgg_echo("org:cantedit"));
		
		forward($_SERVER['HTTP_REFERER']);
		exit;
	}
	
    $org->generateEmailCode();
    	
	forward($org->getUrl() . "mobilesettings/");
	exit;
?>