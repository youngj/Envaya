<?php

    $org = $vars['org'];

    $feedNames = $org->get_related_feed_names();

    echo "<div class='padded'>";

    echo sprintf(
        __('feed:org:about'),
            "<a href='{$org->get_url()}'>".escape($org->name)."</a>",
            "<a href='{$org->get_url()}/partnerships/edit'>".__('widget:partnerships')."</a>"
    );

    echo "</div>";

    echo "<hr>";

    echo view('feed/list',
        array('items' => FeedItem::query_by_feed_names($feedNames, $org)->limit(20)->filter()));
