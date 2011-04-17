<?php

/*
 * Represents a select query for a FeedItem
 */
class Query_SelectFeedItem extends Query_Select
{
    function __construct()
    {
        parent::__construct('feed_items f');
        $this->set_row_class('FeedItem');
        $this->order_by('time_posted DESC');
    }

    function where_visible_to_user()
    {        
        if (!Session::isadminloggedin())
        {
            $this->join('INNER JOIN users u ON u.guid = f.user_guid');        
            $this->where("(u.approval > 0 OR f.user_guid = ?)", Session::get_loggedin_userid());
        }
        return $this;
    }
}