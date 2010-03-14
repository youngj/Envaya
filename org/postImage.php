<?php

    $blogPostId = get_input('blogpost');
    $blogPost = get_entity($blogPostId);
    
    $size = strtolower(get_input('size'));
    if (!in_array($size,array('large','small')))
        $size = "small";    
    
    output_image($blogPost->getImageFile($size));
?>