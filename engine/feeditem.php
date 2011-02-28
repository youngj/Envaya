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