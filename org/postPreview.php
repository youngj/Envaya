<?php
    $postId = (int) get_input('blogpost');

    if ($post = get_entity($postId)) 
    {                  
        header('Content-type; text/javascript');
    
        echo json_encode($post->jsProperties());    
    } 
    else 
    {
        not_found();        
    }
        
?>