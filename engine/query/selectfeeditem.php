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
        if (!Permission_ViewUserSite::has_for_root())
        {
            $this->join('INNER JOIN users u ON u.guid = f.user_guid');        
            
            $user = Session::get_logged_in_user();
            if ($user)
            {
                $this->where('(u.approval > 0 OR f.user_guid = ?)', $user->guid);
            }
            else
            {
                $this->where('u.approval > 0');
            }
        }
        return $this;
    }
}