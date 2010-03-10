<?php
    gatekeeper();
        
    $postid = (int) get_input('blogpost');
    $post = get_entity($postid);
    $title = elgg_echo('blog:editpost');
    
    if ($post && $post->canEdit()) 
    {                   
        $org = $post->getContainerEntity();
        $area1 = elgg_view("org/editPost", array('entity' => $post));
        $body = elgg_view_layout("one_column", org_title($org, $title), $area1);        
    }
    else 
    {
        $body = elgg_view('org/contentwrapper',array('body' => elgg_echo('org:noaccess')));
    }
    
    page_draw($title,$body);      

?>