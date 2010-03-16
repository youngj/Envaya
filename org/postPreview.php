<?php
    $postId = (int) get_input('blogpost');

    if ($post = get_entity($postId)) 
    {                  
        header('Content-type; text/javascript');
    
        echo json_encode(array(
            'guid' => $post->guid,
            'dateText' => $post->getDateText(),
            'imageURL' => $post->getImageURL('small'),
            'snippetHTML' => $post->getSnippetHTML()
        ));    
    } 
    else 
    {
        not_found();        
    }
        
?>