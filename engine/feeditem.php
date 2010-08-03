<?php

class FeedItem
{
    protected $attributes;

    function __construct($id = null)
    {
        $this->attributes = array();

        if (!empty($id))
        {
            if ($id instanceof stdClass) // db row
                $row = $id;
            else
                $row = get_feed_item($id);

            if ($row)
            {
                $objarray = (array) $row;
                foreach($objarray as $key => $value)
                {
                    $this->attributes[$key] = $value;
                }
            }
        }
    }

    function __get($name)
    {
        $res = @$this->attributes[$name];

        if ($name == 'args')
        {
            return json_decode($res, true);
        }
        return $res;
    }

    function __set($name, $value)
    {
        if ($name == 'args')
        {
            $value = json_encode($value);
        }
        $this->attributes[$name] = $value;
        return true;
    }

    public function renderView($mode = '')
    {
        return elgg_view("feed/{$this->action_name}", array('item' => $this, 'mode' => $mode));
    }

    public function getSubjectEntity()
    {
        return get_entity($this->subject_guid);
    }

    public function getUserEntity()
    {
        return get_entity($this->user_guid);
    }

    public function getDateText()
    {
        return friendly_time($this->time_posted);
    }

    public function save()
    {
        save_db_row('feed_items', 'id', $this->attributes['id'], array(
            'feed_name' => $this->feed_name,
            'action_name' => $this->action_name,
            'subject_guid' => $this->subject_guid,
            'user_guid' => $this->user_guid,
            'args' => $this->attributes['args'],
            'time_posted' => $this->time_posted,
            //'featured' => $this->featured,
        ));
    }

    static function queryByFeedNames($feedNames, $excludeUser = null)
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
            $query->where("feed_name in (".implode(',', array_fill(0, $numNames, '?')).")", $feedNames);

            if ($excludeUser)
            {
                $query->where("f.user_guid <> ?", $excludeUser->guid);
            }

            if (!isadminloggedin() && !access_get_show_hidden_status())
            {
                $query->where("(u.approval > 0 || u.guid = ?)", get_loggedin_userid());
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

    static function queryByFeedName($feedName)
    {
        return static::queryByFeedNames(array($feedName), null);
    }
}