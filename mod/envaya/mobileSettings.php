<?php
	gatekeeper();

	$group_guid = get_input('org_guid');
	$group = get_entity($group_guid);
	set_page_owner($group_guid);

	$title = elgg_echo("org:mobilesettings");

	$body = elgg_view_title($title);

	if (($group) && ($group->canEdit()))
	{
		$body .= elgg_view("org/mobileSettings", array('entity' => $group));

	} else {
		$body .= elgg_view('org/contentwrapper',array('body' => elgg_echo('org:noaccess')));
	}

	$body = elgg_view_layout('two_column_left_sidebar', '', $body);

	page_draw($title, $body);
?>