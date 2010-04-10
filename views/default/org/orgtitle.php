<?php

    $icon = @$vars['icon'];
    $link = @$vars['link']; 
    
    if ($icon)
    {
        $img = "<img src='$icon' />";
        if ($link)
        {
            $img = "<a href='$link'>$img</a>";
        }
        echo $img;
    }
    
    $hclass = ($icon) ? 'withicon' : 'withouticon';

    $h1 = "<h2 class='$hclass'>".escape($vars['title'])."</h2>";

    if ($link)
    {
        $h1 = "<a href='$link'>$h1</a>";
    }
    
    echo $h1;      
    
    $subtitle = @$vars['subtitle'];
    
    if ($subtitle)
    {
        echo "<h3 class='$hclass'>".escape($subtitle)."</h3>";
    }        
?>