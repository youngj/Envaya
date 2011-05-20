<?php

class Widget_Tweet extends Widget_FeedItem
{
    function get_feed_name()
    {
        return 'Twitter';
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
