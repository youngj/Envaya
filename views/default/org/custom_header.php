<?php    
    $org = $vars['org'];
    $escTitle = escape($org->name);
    $link = $org->get_url();
    $headerInfo = $org->get_header_props();
    $width = escape(@$headerInfo['width']);
    $height = escape(@$headerInfo['height']);    
    $imgUrl = escape($headerInfo['url']);
    
    echo "<div style='text-align:center;height:{$height}px'><a href='$link'><img width='$width' height='$height' src='$imgUrl' alt='$escTitle' /></a></div>";        