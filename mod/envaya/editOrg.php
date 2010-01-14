<?php
	/**
	 * Elgg Envaya plugin
	 * 
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	gatekeeper();

	$group_guid = get_input('org_guid');
	$group = get_entity($group_guid);
	set_page_owner($group_guid);

	$title = elgg_echo("org:edit");
	
	
	$body = elgg_view_title($title);
	
	if (($group) && ($group->canEdit()))
	{
		$body .= elgg_view("org/editOrg", array('entity' => $group));
			 
	} else {
		$body .= elgg_view('org/contentwrapper',array('body' => elgg_echo('org:noaccess')));
	}
	
	$body = elgg_view_layout('two_column_left_sidebar', '', $body);
	
	page_draw($title, $body);
?>