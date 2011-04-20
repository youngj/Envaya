<?php
    $feedItems = $vars['items'];
    
    if (empty($feedItems))
    {
        echo "<div class='padded'>".__("feed:noresults")."</div>";
    }

    foreach ($feedItems as $feedItem)
    {
        $vars['item'] = $feedItem;    
        echo view('feed/item', $vars);
    }
