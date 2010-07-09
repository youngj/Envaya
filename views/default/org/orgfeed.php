<?php

    $org = $vars['org'];

    $feedNames = $org->getRelatedFeedNames();

    echo "<div class='padded'>";

    echo sprintf(
        elgg_echo('feed:org:about'),
            "<a href='{$org->getURL()}'>".escape($org->name)."</a>",
            "<a href='{$org->getURL()}/partnerships/edit'>".elgg_echo('widget:partnerships')."</a>"
    );

    echo "</div>";

    echo "<hr>";

    echo elgg_view('feed/list',
        array('items' => FeedItem::filterByFeedNames($feedNames, $org, $limit = 20)));
