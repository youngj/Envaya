<?php
    $items = $vars['items'];
    $separator = @$vars['separator'] ?: ": ";
    if (!isset($vars['include_last']))
    {
        $include_last = true;
    }    
    else
    {
        $include_last = $vars['include_last'];
    }
    
    $num_items = sizeof($items);
    
    $list = array();
    
    for ($i = 0; $i < $num_items; $i++)
    {
        $item = $items[$i];
        $url = escape($item['url']);
        $title = escape($item['title']);        
        if ($i < $num_items - 1)
        {
            $list[] = "<a href='$url'>$title</a>";
        }
        else if ($include_last)
        {
            $list[] = $title;
        }
    }
    
    echo implode($separator, $list);
