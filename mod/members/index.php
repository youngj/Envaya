<?php

	/**
	 * Elgg members index page
	 * 
	 * @package ElggMembers
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	// Load Elgg engine
		require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
	// Get the current page's owner
		$page_owner = page_owner_entity();
		if ($page_owner === false || is_null($page_owner)) {
			$page_owner = $_SESSION['user'];
			set_page_owner($page_owner->getGUID());
		}
		
	//get filter
		$limit = get_input('limit', 20);
		$offset = get_input('offset', 0);
		$filter = get_input("filter");
		if(!$filter)
			$filter = "newest";
	
	// search options
		$tag = get_input('tag');
		
	//search members
		$area1 = elgg_view("members/search");
		
	//user name search
		if($search_name)
			$area1 .= search_for_user($search_name, $limit, 0, "", false);
			
	//user search on tag
		if($search_tag)
			$area1 .= list_user_search($search_tag, $limit);
		
	// count members
		$members = get_number_users();
		
	// title
	    $area2 = elgg_view_title(elgg_echo("members:members"));
	    
	//get the correct view based on filter
		switch($filter){
			case "newest":
			$content = list_entities("user","",0,10,false);
			break;
			case "pop":
			$content = list_entities_by_relationship_count('friend', true, '', '', 0, 10, false);
			break;
			case "active":
			$content = elgg_view("members/online");
			break;
			case "search":
			set_context('search');
			$content = list_user_search($tag);
			break;
			case "search_tags":
			$content = trigger_plugin_hook('search','',$tag,"");
			$content .= list_entities_from_metadata("", $tag, "user", "", "", 10, false, false);
			break;
			case 'default':
			$content = list_entities("user","",0,10,false);
			break;
		}
    
		$area2 .= elgg_view('page_elements/contentwrapper',array('body' => elgg_view("members/members_sort_menu", array("count" => $members, "filter" => $filter)) . $content, 'subclass' => 'members'));
		
    //select the correct canvas area
	    $body = elgg_view_layout("sidebar_boxes", $area1, $area2);
		
	// Display page
		page_draw(sprintf(elgg_echo('members:members'),$page_owner->name),$body);
		
?>
