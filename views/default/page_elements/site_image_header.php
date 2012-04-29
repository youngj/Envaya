<?php    
    $header_image = @$vars['design']['header_image'];

    if ($header_image)
    {
        $escUrl = escape($vars['site_url']);
        $width = escape(@$header_image['width']);
        $height = escape(@$header_image['height']);    
        $imgUrl = escape($header_image['url']);
        $escTitle = escape($vars['site_name']);
        
        echo "<div style='text-align:center;height:{$height}px'><a href='$escUrl'><img width='$width' height='$height' src='$imgUrl' alt='$escTitle' /></a></div>";
    }