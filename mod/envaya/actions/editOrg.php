<?php
	// Load configuration
	global $CONFIG;
	
    gatekeeper();
	action_gatekeeper();

	$input = array();
    
	foreach($CONFIG->org_profile_fields as $shortname => $valuetype) 
    {
		$input[$shortname] = get_input($shortname);
        
		if ($valuetype == 'tags')
			$input[$shortname] = string_to_tag_array($input[$shortname]);
	}
	
    $org_guid = get_input('org_guid');
	$org = get_entity($org_guid);
	if (!$org || !$org->canEdit())
	{
		register_error(elgg_echo("org:cantedit"));		
		forward();
		exit;
	}
		
    foreach($input as $shortname => $value) {
        $org->$shortname = $value;
    }
	
    if ($org->location)
    {
        $latlong = elgg_geocode_location($org->location);
    
        if ($latlong)
        {
            $org->setLatLong($latlong['lat'], $latlong['long']);
        }            
    }

	$org->save();
	
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