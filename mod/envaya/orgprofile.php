<?php

	$org_guid = get_input('org_guid');
	set_context('org');

	global $autofeed;
	$autofeed = false;
   
	$org = get_entity($org_guid);
	if ($org) {
		set_page_owner($org_guid);                
        
		$title = $org->name;	

        $viewOrg = false;
        
        if ($org->approval > 0)
        {
            //organization approved
        }
	    else if ($org->approval < 0)
	    {
            system_message(elgg_echo('org:rejected'));
        }
        else
        {
            system_message(elgg_echo('org:waitingapproval'));            
        }
                
        $area2 = elgg_view('org/org', array('entity' => $org, 'user' => $_SESSION['user'], 'full' => $org->userCanSee()));                
        $body = elgg_view_layout('one_column', org_title($org, $org->location), $area2);
        
	} else {
		$title = elgg_echo('org:notfound');
        $body = elgg_view_layout('one_column_padded', elgg_view_title($title), elgg_echo('org:notfound:details'));
	}

	page_draw($title, $body);
?>