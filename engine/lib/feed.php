<?php

function get_feed_name($conditions)
{
    ksort($conditions);

    $encConditions = array();

    foreach ($conditions as $name => $value)
    {
        if (!is_null($value) && $value !== '')
        {
            $encConditions[] = "$name=".urlencode($value);
        }
    }
    return implode("&", $encConditions);
}

function feed_row_to_feed_item($row)
{
    return new FeedItem($row);
}

function post_feed_items($user, $actionName, $subject, $args = null, $time = null)
{
    if (!$time)
    {
        $time = time();
    }

    $feedNames = $user->getFeedNames();

    if ($subject instanceof User && $subject != $user)
    {
        $feedNames = $feedNames + $subject->getFeedNames();
        $feedNames = array_flip(array_flip($feedNames));
    }

    foreach ($feedNames as $feedName)
    {
        $feedItem = new FeedItem();
        $feedItem->feed_name = $feedName;
        $feedItem->action_name = $actionName;
        $feedItem->subject_guid = $subject->guid;
        $feedItem->user_guid = $user->guid;
        $feedItem->time_posted = $time;
        $feedItem->args = $args;

        $feedItem->save();
    }
}