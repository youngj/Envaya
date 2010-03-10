<?php

    global $CONFIG;

    $blogPostId = get_input('blogpost');
    $blogPost = get_entity($blogPostId);
    
    $size = strtolower(get_input('size'));
    if (!in_array($size,array('large','small')))
        $size = "small";    
    
    $filehandler = $blogPost->getImageFile($size);
    
    $success = false;
    if ($filehandler->open("read")) {
        if ($contents = $filehandler->read($filehandler->size())) {
            $success = true;
        } 
    }
    
    header("Content-type: image/jpeg");
    header('Expires: ' . date('r',time() + 864000));
    header("Pragma: public");
    header("Cache-Control: public");
    header("Content-Length: " . strlen($contents));
    echo $contents;
?>