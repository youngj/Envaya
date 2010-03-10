<?php
	gatekeeper();

	$group_guid = get_input('org_guid');
	$group = get_entity($group_guid);
	set_page_owner($group_guid);

	$title = elgg_echo("org:edit");

	if (($group) && ($group->canEdit()))
	{
		$body = elgg_view("org/editOrg", array('entity' => $group));
	} 
    else 
    {
		$body = elgg_view('org/contentwrapper',array('body' => elgg_echo('org:noaccess')));
	}

    $body = elgg_view_layout('one_column',  org_title($org, $title), $body);

	page_draw($title, $body);
?>