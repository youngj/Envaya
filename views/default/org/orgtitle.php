<?php

    $icon = @$vars['icon'];
    $link = @$vars['link']; 
    
    $escTitle = escape($vars['title']);
    
    $customHeader = @$vars['customHeader'];
    
    if ($customHeader)
    {
        echo "<a href='$link'><img src='$customHeader' alt='$escTitle' /></a>";    
    }
    else
    {
        if ($icon)
        {
            $img = "<img src='$icon' alt='$escTitle' />";
            if ($link)
            {
                $img = "<a href='$link'>$img</a>";
            }
            echo $img;
        }

        $hclass = ($icon) ? 'withicon' : 'withouticon';

        $h1 = "<h2 class='$hclass'>$escTitle</h2>";

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
    }    
?>