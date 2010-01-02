<?php

	/**
	 * Elgg blog: add post action
	 * 
	 * @package ElggBlog
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 */

	// Make sure we're logged in (send us to the front page if not)
		gatekeeper();

        // Make sure action is secure
        action_gatekeeper();

	// Get input data
		$title = get_input('blogtitle');
		$body = get_input('blogbody');
		$tags = get_input('blogtags');
		$access = get_input('access_id');
		$comments_on = get_input('comments_select','Off');

	// Cache to the session
		$_SESSION['user']->blogtitle = $title;
		$_SESSION['user']->blogbody = $body;
		$_SESSION['user']->blogtags = $tags;
		
	// Convert string of tags into a preformatted array
		$tagarray = string_to_tag_array($tags);
		
	// Make sure the title / description aren't blank
		if (empty($title) || empty($body)) {
			register_error(elgg_echo("blog:blank"));
			forward($_SERVER['HTTP_REFERER']);
			
	// Otherwise, save the blog post 
		} else {
			
	// Initialise a new ElggObject
			$blog = new ElggObject();
	// Tell the system it's a blog post
			$blog->subtype = "blog";
	// Set its owner to the current user
			$blog->owner_guid = $_SESSION['user']->getGUID();
	// Set it's container		
			$blog->container_guid = (int)get_input('container_guid', $_SESSION['user']->getGUID());
	// For now, set its access to public (we'll add an access dropdown shortly)
			$blog->access_id = $access;
	// Set its title and description appropriately
			$blog->title = $title;
			$blog->description = $body;
	// Before we can set metadata, we need to save the blog post
			if (!$blog->save()) {
				register_error(elgg_echo("blog:error"));
				forward($_SERVER['HTTP_REFERER']);
			}
	// Now let's add tags. We can pass an array directly to the object property! Easy.
			if (is_array($tagarray)) {
				$blog->tags = $tagarray;
			}
			$blog->comments_on = $comments_on; //whether the users wants to allow comments or not on the blog post

	// Success message
			system_message(elgg_echo("blog:posted"));
	// add to river
	        add_to_river('river/object/blog/create','create',$_SESSION['user']->guid,$blog->guid);
	// Remove the blog post cache
			//unset($_SESSION['blogtitle']); unset($_SESSION['blogbody']); unset($_SESSION['blogtags']);
			remove_metadata($_SESSION['user']->guid,'blogtitle');
			remove_metadata($_SESSION['user']->guid,'blogbody');
			remove_metadata($_SESSION['user']->guid,'blogtags');
			
	// Forward to the main blog page
			$page_owner = get_entity($blog->container_guid);
			if ($page_owner instanceof ElggUser)
				$username = $page_owner->username;
			else if ($page_owner instanceof ElggGroup)
				$username = "group:" . $page_owner->guid;
			forward("pg/blog/$username");
				
		}
		
?>
