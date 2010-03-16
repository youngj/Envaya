<?php

    $post = (int) get_input('blogpost');

    if ($blogpost = get_entity($post)) 
    {        
        $canedit = $blogpost->canEdit();
        if ($canedit) 
        {
            add_submenu_item(elgg_echo("blog:editpost"), "{$blogpost->getUrl()}/edit", 'b');                    
        }
    
        $page_owner = $blogpost->getContainerEntity();
            
        $area2 = elgg_view_entity($blogpost, true);

        $title = elgg_echo('org:news');

        $body = elgg_view_layout("one_column_padded", org_title($page_owner, $title), $area2);            
        
        page_draw($title,$body);
    } 
    else 
    {
        not_found();        
    }
        
?>