<?php
    $items = $vars['items'];
    $separator = @$vars['separator'] ?: ": ";
    $num_items = sizeof($items);
    
    for ($i = 0; $i < $num_items; $i++)
    {
        $item = $items[$i];
        $url = escape($item['url']);
        $title = escape($item['title']);        
        if ($i < $num_items - 1)
        {
            echo "<a href='$url'>$title</a>$separator";
        }
        else
        {
            echo $title;
        }
    }
