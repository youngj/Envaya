<?php
	gatekeeper();

	$group_guid = get_input('org_guid');
	$group = get_entity($group_guid);
	set_page_owner($group_guid);
	
	if (($group) && ($group->canEdit()))
	{
	    
	    $entityLat = $group->getLatitude();
        if (!empty($entityLat)) 
        { 
            $body = elgg_view_layout('section', elgg_echo("org:map"), 
                elgg_view("org/map", array(
                    'lat' => $entityLat, 
                    'long' => $group->getLongitude(),
                    'zoom' => 10,
                    'pin' => true,
                    'org' => $group,
                    'edit' => true
                ))
            );        
        }
        else
        {
            $body = elgg_view_layout('section', elgg_echo("org:map"), 
                elgg_view("org/map", array(
                    'lat' => 0.0, 
                    'long' => 0.0,
                    'zoom' => 1,
                    'pin' => false,
                    'org' => $group,
                    'edit' => true
                ))
            );
        }

	} 
    else 
    {
		$body = elgg_view('org/contentwrapper',array('body' => elgg_echo('org:noaccess')));
	}

    $title = elgg_echo("org:editmap");
    $body = elgg_view_layout('one_column', org_title($org, $title), $body);

	page_draw($title, $body);
	
?>