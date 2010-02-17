<?php

    $post = (int) get_input('blogpost');

    if ($blogpost = get_entity($post)) 
    {        
        $page_owner = $blogpost->getContainerEntity();
            
        $area2 = elgg_view_entity($blogpost, true);

        $title = elgg_echo('org:updates');

        $body = elgg_view_layout("one_column_padded", org_title($page_owner, $title), $area2);            
    } 
    else 
    {
        $body = elgg_view("blog/notfound");
        $title = elgg_echo("blog:notfound");
    }
        
    page_draw($title,$body);
        
?>