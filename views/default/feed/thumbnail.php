<?php   
    $thumbnail_url = $vars['thumbnail_url'];        
    $link_url = $vars['link_url'];    
    if ($thumbnail_url)
    {
        echo "<a class='feed_image_link' href='$link_url'><img src='$thumbnail_url' /></a>";
    }
