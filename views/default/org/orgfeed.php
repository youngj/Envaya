<?php

    $org = $vars['org'];

    $feedNames = $org->getRelatedFeedNames();

    echo "<div class='padded'>";

    echo sprintf(
        __('feed:org:about'),
            "<a href='{$org->getURL()}'>".escape($org->name)."</a>",
            "<a href='{$org->getURL()}/partnerships/edit'>".__('widget:partnerships')."</a>"
    );

    echo "</div>";

    echo "<hr>";

    echo elgg_view('feed/list',
        array('items' => FeedItem::queryByFeedNames($feedNames, $org)->limit(20)->filter()));
