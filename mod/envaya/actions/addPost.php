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

    gatekeeper();
    action_gatekeeper();

    $body = get_input('blogbody');
            
    if (empty($body)) {
        register_error(elgg_echo("blog:blank"));
        forward($_SERVER['HTTP_REFERER']);
    } 
    else 
    {            
        $blog = new ElggObject();
        $blog->subtype = "blog";
        $blog->owner_guid = $_SESSION['user']->getGUID();
        $blog->container_guid = (int)get_input('container_guid');
        $blog->access_id = 2;
        $blog->description = $body;
    
        if (!$blog->save()) {
            register_error(elgg_echo("blog:error"));
            forward($_SERVER['HTTP_REFERER']);
        }

            system_message(elgg_echo("blog:posted"));
            
        $page_owner = get_entity($blog->container_guid);
        forward($page_owner->getUrl() . "blog");
    }
        
?>
