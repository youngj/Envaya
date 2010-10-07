<?php
    $feedItems = $vars['items'];
    $mode = @$vars['mode'];

    if (empty($feedItems))
    {
        echo "<div class='padded'>".__("search:noresults")."</div>";
    }

    foreach ($feedItems as $feedItem)
    {
        echo view('feed/item', array('item' => $feedItem, 'mode' => $mode));
    }
