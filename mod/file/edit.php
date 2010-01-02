<?php
	/**
	 * Elgg file saver
	 * 
	 * @package ElggFile
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	gatekeeper();

	// Render the file upload page
	
	$file = (int) get_input('file_guid');
	if ($file = get_entity($file)) {
		
		// Set the page owner
		$page_owner = page_owner_entity();
		if ($page_owner === false || is_null($page_owner)) {
			$container_guid = $file->container_guid;
			if (!empty($container_guid))
				if ($page_owner = get_entity($container_guid)) {
					set_page_owner($container_guid->guid);
				}
			if (empty($page_owner)) {
				$page_owner = $_SESSION['user'];
				set_page_owner($_SESSION['guid']);
			}
		}
		$title = elgg_view_title($title = elgg_echo('file:edit'));
		if ($file->canEdit()) { 
    		$area2 = elgg_view("file/upload",array('entity' => $file));
			$body = elgg_view_layout('two_column_left_sidebar', '', $title . $area2);
			page_draw(elgg_echo("file:upload"), $body);
		}
	} else {
		forward();
	}
	
?>