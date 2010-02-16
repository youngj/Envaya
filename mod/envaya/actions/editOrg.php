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

	// Load configuration
	global $CONFIG;
	
	action_gatekeeper();

	// Get group fields
	$input = array();
	foreach($CONFIG->org_fields as $shortname => $valuetype) {
		$input[$shortname] = get_input($shortname);
        
		if ($valuetype == 'tags')
			$input[$shortname] = string_to_tag_array($input[$shortname]);
	}
	
	$user_guid = get_input('user_guid');
	$user = NULL;
	if (!$user_guid) $user = $_SESSION['user'];
	else
		$user = get_entity($user_guid);
		
	$org_guid = get_input('org_guid');
	
	$org = new Organization($org_guid); // load if present, if not create a new org
	if (($org_guid) && (!$org->canEdit()))
	{
		register_error(elgg_echo("org:cantedit"));
		
		forward($_SERVER['HTTP_REFERER']);
		exit;
	}
	
	// Assume we can edit or this is a new org
	if (sizeof($input) > 0)
	{
		foreach($input as $shortname => $value) {
			$org->$shortname = $value;
		}
	}
	
	// Validate create
	if (!$org->name)
	{
		register_error(elgg_echo("org:notitle"));
		
		forward($_SERVER['HTTP_REFERER']);
		exit;
	}
	
	// Group membership
    $org->membership = ACCESS_PUBLIC; 
	
	// Set access - all Organizations are public from elgg's point of view
    $org->access_id = 2;
	
	// Set group tool options
	//$group->files_enable = get_input('files_enable', 'yes');
	//$group->pages_enable = get_input('pages_enable', 'yes');
	//$group->forum_enable = get_input('forum_enable', 'yes');	

    if ($org->location)
    {
        $latlong = elgg_geocode_location($org->location);
    
        if ($latlong)
        {
            //echo "lat=".$latlong['lat'];
        
            $org->setLatLong($latlong['lat'], $latlong['long']);
        }            
    }

	$org->save();
	
	if (!$org->isMember($user))
		$org->join($user); // Creator always a member
	
	
	// Now see if we have a file icon
	if ((isset($_FILES['icon'])) && (substr_count($_FILES['icon']['type'],'image/')))
	{
		$prefix = "envaya/".$org->guid;
		
		$filehandler = new ElggFile();
		$filehandler->owner_guid = $org->owner_guid;
		$filehandler->setFilename($prefix . ".jpg");
		$filehandler->open("write");
		$filehandler->write(get_uploaded_file('icon'));
		$filehandler->close();
		
		$thumbtiny = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),25,25, true);
		$thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),40,40, true);
		$thumbmedium = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),100,100, true);
		$thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),200,200, false);
		if ($thumbtiny) {
			
			$thumb = new ElggFile();
			$thumb->owner_guid = $group->owner_guid;
			$thumb->setMimeType('image/jpeg');
			
			$thumb->setFilename($prefix."tiny.jpg");
			$thumb->open("write");
			$thumb->write($thumbtiny);
			$thumb->close();
			
			$thumb->setFilename($prefix."small.jpg");
			$thumb->open("write");
			$thumb->write($thumbsmall);
			$thumb->close();
			
			$thumb->setFilename($prefix."medium.jpg");
			$thumb->open("write");
			$thumb->write($thumbmedium);
			$thumb->close();
			
			$thumb->setFilename($prefix."large.jpg");
			$thumb->open("write");
			$thumb->write($thumblarge);
			$thumb->close();
				
		}
	}
	
	system_message(elgg_echo("org:saved"));
	
	forward($org->getUrl());
	exit;
?>