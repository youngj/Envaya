<?php

/*
 * Represents a tweet imported from a Twitter feed using ExternalFeed_Twitter.
 */
class Widget_Tweet extends Widget_FeedItem
{
    function get_feed_name()
    {
        return 'Twitter';
    }
    
    function get_content_view()
    {
        return 'widgets/tweet_view_content';
    }    
    
    function get_title()
    {
        return 'Tweet';
    }    
    
    function get_title_view()
    {
        return 'empty';
    }    
}
