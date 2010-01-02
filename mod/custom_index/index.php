<?php

	/**
	 * Elgg custom index
	 * 
	 * @package ElggCustomIndex
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008
	 * @link http://elgg.com/
	 */

	// Get the Elgg engine
		require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
    //get required data		
	set_context('search');//display results in search mode, which is list view
	//grab the latest 4 blog posts. to display more, change 4 to something else
	$blogs = list_entities('object','blog',0,4,false, false, false);
	//grab the latest bookmarks
	$bookmarks = list_entities('object','bookmarks',0,4,false, false, false);
	//grab the latest files
	$files = list_entities('object','file',0,4,false, false, false);
	//get the newest members who have an avatar
	$newest_members = get_entities_from_metadata('icontime', '', 'user', '', 0, 10);
	//newest groups
	$groups = list_entities('group','',0,4,false, false, false);
	//grab the login form
	$login = elgg_view("account/forms/login");
	
    //display the contents in our new canvas layout
	$body = elgg_view_layout('new_index',$login, $files, $newest_members, $blogs, $groups, $bookmarks);
   
    page_draw($title, $body);
		
?>