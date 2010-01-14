<?php

    /**
     * Elgg blog index page
     * 
     * @package ElggBlog
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Curverider Ltd <info@elgg.com>
     * @copyright Curverider Ltd 2008-2009
     * @link http://elgg.com/
     */

    //set blog title
    
        $org_guid = get_input("org_guid");
        $org = get_entity($org_guid);
        set_page_owner($org_guid);
    
        $area2 = '';
        
        // Get a list of blog posts        
        $area2 .= list_user_objects($org->getGUID(),'blog',10,false);

        // Get blog tags
        // Get categories, if they're installed
        global $CONFIG;
        $area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?subtype=blog&owner_guid='.$page_owner->guid.'&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));
        
        // Display them in the page
        $body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);
        
        // Display page
        page_draw(sprintf(elgg_echo('blog:user'),$org->name),$body);
        
?>