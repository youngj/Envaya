<?php

class FeedItem extends Model
{
    static $table_name = 'feed_items';
    static $table_attributes = array(
        'feed_name' => '',
        'action_name' => '',
        'subject_guid' => 0,
        'user_guid' => 0,
        'args' => '',
        'time_posted' => 0,        
    );   
    
    function get($name)
    {
        $res = parent::get($name);

        if ($name == 'args')
        {
            return json_decode($res, true);
        }
        return $res;
    }

    function set($name, $value)
    {
        if ($name == 'args')
        {
            $value = json_encode($value);
        }
        parent::set($name, $value);
    }

    public function render_view($mode = '')
    {
        return view("feed/{$this->action_name}", array('item' => $this, 'mode' => $mode));
    }

    public function get_subject_entity()
    {
        return get_entity($this->subject_guid);
    }

    public function get_user_entity()
    {
        return get_entity($this->user_guid);
    }

    public function get_date_text()
    {
        return friendly_time($this->time_posted);
    }
    
    function query_items_in_group()
    {
        return FeedItem::query()
            ->where('action_name = ?', $this->action_name)
            ->where('subject_guid = ?', $this->subject_guid)
            ->where('time_posted = ?', $this->time_posted);
    }

    static function query_by_feed_names($feedNames, $excludeUser = null)
    {
        $numNames = sizeof($feedNames);

        $query = new Query_Select('feed_items f');
        $query->join('INNER JOIN users_entity u ON u.guid = f.user_guid');
        
        if ($numNames == 0)
        {
            $query->where("8<3");            
        }
        else
        {
            $query->where_in("feed_name", $feedNames);

            if ($excludeUser)
            {
                $query->where("f.user_guid <> ?", $excludeUser->guid);
            }

            if (!Session::isadminloggedin())
            {
                $query->where("(u.approval > 0 || u.guid = ?)", Session::get_loggedin_userid());
            }
        }

        if ($numNames > 1)
        {
            $query->group_by('action_name, subject_guid, time_posted');
        }

        $query->order_by('time_posted DESC');
        $query->set_row_function('feed_row_to_feed_item');
        return $query;
    }

    static function query_by_feed_name($feedName)
    {
        return static::query_by_feed_names(array($feedName), null);
    }
}