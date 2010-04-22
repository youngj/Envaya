<?php    
    $org = $vars['org'];
    $escTitle = escape($org->name);
    $link = $org->getURL();
    $headerInfo = $org->getHeader();
    $width = escape(@$headerInfo['width']);
    $height = escape(@$headerInfo['height']);
    
    $imgUrl = $org->getHeaderURL('large');
    
    echo "<div style='text-align:center;height:{$height}px'><a href='$link'><img width='$width' height='$height' src='$imgUrl' alt='$escTitle' /></a></div>";        