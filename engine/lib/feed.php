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

    public function renderView()
    {
        return elgg_view("feed/{$this->action_name}", array('item' => $this));
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
        if ($this->id > 0)
        {
			return insert_data("UPDATE feed_items set feed_name=?, action_name=?, subject_guid=?, user_guid=?, args=?, time_posted=? WHERE id = ?",
                array($this->feed_name, $this->action_name, $this->subject_guid, $this->user_guid, $this->attributes['args'], $this->time_posted, $this->id)
            );
        }
        else
        {
            return insert_data("INSERT into feed_items (feed_name, action_name, subject_guid, user_guid, args, time_posted) VALUES (?,?,?,?,?,?)",
                array($this->feed_name, $this->action_name, $this->subject_guid, $this->user_guid, $this->attributes['args'], $this->time_posted)
            );
        }
    }


	static function filterByFeedName($feedName, $limit = 10, $offset = 0, $count = false)
	{
		$where = array("feed_name = ?");
		$args = array($feedName);

		if (!isadminloggedin() && !access_get_show_hidden_status())
		{
			$where[] = "(u.approval > 0 || u.guid = ?)";
			$args[] = get_loggedin_userid();
		}

		$from = "FROM feed_items f INNER JOIN users_entity u ON u.guid = f.user_guid WHERE ".implode(" AND ", $where);

		if ($count)
		{
			return get_data_row("SELECT COUNT(*) as total $from", $args)->total;
		}
		else
		{
			return array_map('feed_row_to_feed_item',
				get_data("SELECT f.* $from ORDER BY time_posted DESC LIMIT ".((int)$offset).", ".((int)$limit), $args)
			);
		}

	}
}

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

	if ($subject instanceof ElggUser && $subject != $user)
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