<?php
    gatekeeper();
        
    $postid = (int) get_input('blogpost');
    $post = get_entity($postid);
    $title = elgg_echo('blog:editpost');
    
    set_theme('editor');
    set_context('editor');
    
    if ($post && $post->canEdit()) 
    {                   
        $cancelUrl = get_input('from') ?: $post->getUrl();

        add_submenu_item(elgg_echo("canceledit"), $cancelUrl, 'edit');                
    
        $org = $post->getContainerEntity();
        $area1 = elgg_view("org/editPost", array('entity' => $post));
        $body = elgg_view_layout("one_column_padded", elgg_view_title($title), $area1);        
    }
    else 
    {
        not_found();
    }
    
    page_draw($title,$body);      

?>