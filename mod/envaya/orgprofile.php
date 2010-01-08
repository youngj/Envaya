<?php
	/**
	 * Full org profile
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	$org_guid = get_input('org_guid');
	set_context('org');

	global $autofeed;
	$autofeed = true;

	$org = get_entity($org_guid);
	if ($org) {
		set_page_owner($org_guid);
                
        //$org->setLatLong(-5.216667,39.733333);        
        //$org->save();

		$title = $org->name;

		// Hide some items from closed orgs when the user is not logged in.
		$view_all = true;

		$area2 = elgg_view_title($title);
		$area2 .= elgg_view('org/org', array('entity' => $org, 'user' => $_SESSION['user'], 'full' => true));

		$body = elgg_view_layout('two_column_left_sidebar', $area1, $area2, $area3);
	} else {
		$title = elgg_echo('org:notfound');

		$area2 = elgg_view_title($title);
		$area2 .= elgg_view('org/contentwrapper',array('body' => elgg_echo('org:notfound:details')));

		$body = elgg_view_layout('two_column_left_sidebar', "", $area2,"");
	}

	// Finally draw the page
	page_draw($title, $body);
?>