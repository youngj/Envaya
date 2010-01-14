<?php

    /**
     * Elgg blog add entry page
     * 
     * @package ElggBlog
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Curverider Ltd <info@elgg.com>
     * @copyright Curverider Ltd 2008-2009
     * @link http://elgg.com/
     */

    // Load Elgg engine        
        gatekeeper();
        
    // Get the current page's owner
        $org_guid = get_input('org_guid');
        $org = get_entity($org_guid);
        set_page_owner($org_guid);
            
    //set the title
        $area1 = elgg_view_title(elgg_echo('blog:addpost'));

    // Get the form
        $area1 .= elgg_view("blog/edit", array('container_guid' => $org_guid));
        
    // Display page
        page_draw(elgg_echo('blog:addpost'),elgg_view_layout("edit_layout", $area1));

        
?>