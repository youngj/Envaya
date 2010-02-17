<?php
	/**
	 * Elgg envaya plugin
	 * 
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	gatekeeper();

	// Render the file upload page
	$title = elgg_echo("org:new");
	
    $body = elgg_view_layout('one_column', elgg_view_title($title), elgg_view("org/editOrg"));
	
	page_draw($title, $body);
?>