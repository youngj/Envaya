<?php

/* 
 * Represents an action that a user has performed on Envaya, 
 * which is published for other people to read about.
 * 
 * Each action may result in multiple FeedItems with a different feed_name. 
 * There are many feeds each with their own feed_name, e.g.:
 * - all organizations in a sector (e.g. Environment)
 * - all organizations in a region (e.g. Dar es Salaam)
 * - all organizations in a sector/region combination (e.g. Environment and Dar es Salaam)
 * - all organizations
 * - one particular organization 
 *
 * Each FeedItem is associated with a particular user (user_guid).
 * action_name refers to a FeedItemHandler subclass defined in engine/feeditemhandler/
 * which is responsible for rendering a view of the feed item.
 *
 * Depending on the action, it may be associated with another Entity (subject_guid)
 * or other arbitrary properties (args).
 */
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
    
    function can_edit()
    {
        $user = Session::get_loggedin_user();
        if (!$user)
            return false;
    
        return $user->admin || $user->guid == $this->subject_guid || $user->guid == $this->user_guid;
    }

    function set($name, $value)
    {
        if ($name == 'args')
        {
            $value = json_encode($value);
        }
        parent::set($name, $value);
    }
    
    function get_handler()
    {
        if (!$this->handler)
        {    
            try
            {
                $action_name = str_replace('_', '', $this->action_name);           
                $handlerCls = new ReflectionClass('FeedItemHandler_'.$action_name);
                $this->handler = $handlerCls->newInstance();                        
            }
            catch (ReflectionException $ex)
            {        
                $this->handler = new FeedItemHandler();
            }        
        }
        return $this->handler;
    }    
    
    public function is_valid()
    {
        return $this->get_handler()->is_valid($this);
    }

    public function render_heading($mode = '')
    {
        return $this->get_handler()->render_heading($this, $mode);    
    }

    public function render_thumbnail($mode = '')
    {
        return $this->get_handler()->render_thumbnail($this, $mode);    
    }    
    
    public function render_content($mode = '')
    {
        return $this->get_handler()->render_content($this, $mode);
    }

    public function get_subject_entity()
    {
        return Entity::get_by_guid($this->subject_guid);
    }

    public function get_user_entity()
    {
        return User::get_by_guid($this->user_guid);
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

    static function query()
    {
        return new Query_SelectFeedItem();
    }
    
    static function query_by_feed_names($feedNames)
    {
        $query = static::query();        
        $query->where_in("feed_name", $feedNames);

        if (sizeof($feedNames) > 1)
        {
            $query->group_by('action_name, subject_guid, time_posted');
        }
        
        return $query;
    }

    static function query_by_feed_name($feedName)
    {
        $query = static::query();
        $query->where("feed_name = ?", $feedName);
        return $query;    
    }

    static function make_feed_name($conditions)
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
    
    static function post($user, $actionName, $subject, $args = null, $time = null)
    {
        if (!$time)
        {
            $time = time();
        }

        $feedNames = $user->get_feed_names();

        if ($subject != $user && method_exists($subject, 'get_feed_names'))
        {
            $feedNames = $feedNames + $subject->get_feed_names();
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
}