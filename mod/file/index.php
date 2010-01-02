<?php
	/**
	 * Elgg file browser
	 * 
	 * @package ElggFile
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 * 
	 * 
	 * TODO: File icons, download & mime types
	 */

	//require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	if (is_callable('group_gatekeeper')) group_gatekeeper();
	
	//set the title
		if(page_owner() == $_SESSION['user']){
			$area2 = elgg_view_title($title = elgg_echo('file:yours'));
		}else{
			$area2 = elgg_view_title($title = elgg_echo('files'));
		}

	// Get objects
		set_context('search');
		$area2 .= list_entities("object","file",page_owner(),10);
		set_context('file');
		$get_filter = get_filetype_cloud(page_owner());
		if($get_filter)
			$area1 = $get_filter;
		else
			$area2 .= elgg_view('page_elements/contentwrapper',array('body' => elgg_echo("file:none")));

		$body = elgg_view_layout('two_column_left_sidebar', $area1, $area2);
	
	// Finally draw the page
		page_draw(sprintf(elgg_echo("file:user"),page_owner_entity()->name), $body);
?>