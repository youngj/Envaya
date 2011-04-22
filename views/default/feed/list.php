<?php
    $feedItems = $vars['items'];
    
    $elements = array();
    foreach ($feedItems as $feedItem)
    {
        if ($feedItem->is_valid())
        {
            $vars['item'] = $feedItem;    
            $elements[] = view('feed/item', $vars);
        }
    }
    
    if (sizeof($elements))
    {
        echo implode("<div class='separator'></div>", $elements);
    }
    else
    {
        echo "<div class='padded'>".__("feed:noresults")."</div>";
    }