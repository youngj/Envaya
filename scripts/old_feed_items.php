<?php

require_once("scripts/cmdline.php");
require_once("engine/start.php");

Config::set('debug', false);

$feedItems = FeedItem::filterByFeedName('', $limit = 500);
foreach ($feedItems as $feedItem)
{
    $feedName = "user={$feedItem->user_guid}";

    $row = Database::get_row("select * from feed_items where feed_name=? and subject_guid=? and time_posted=?",
        array($feedName, $feedItem->subject_guid, $feedItem->time_posted)
    );
    if ($row)
    {
        echo "1";
    }
    else
    {
        $id = 0;
        Database::save_row('feed_items', 'id', $id, array(
            'feed_name' => $feedName,
            'action_name' => $feedItem->action_name,
            'subject_guid' => $feedItem->subject_guid,
            'user_guid' => $feedItem->user_guid,
            'args' => $feedItem->attributes['args'],
            'time_posted' => $feedItem->time_posted,
        ));

        echo "0";
    }
}

echo "\n";