<?php

/*
 * Represents a select query for an FeedItem
 */
class Query_SelectFeedItem extends Query_Select
{
    function __construct()
    {
        parent::__construct('feed_items f');
        $this->set_row_function('feed_row_to_feed_item');
        $this->order_by('time_posted DESC');
    }

    function where_visible_to_user()
    {        
        $this->join('INNER JOIN users_entity u ON u.guid = f.user_guid');        
        if (!Session::isadminloggedin())
        {
            $this->where("(u.approval > 0 || u.guid = ?)", Session::get_loggedin_userid());
        }
        return $this;
    }
}