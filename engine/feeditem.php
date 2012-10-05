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
 * subtype_id refers to a FeedItem subclass (see engine/feeditem/)
 *
 * Depending on the action, it may be associated with another Entity (subject_guid)
 * or other arbitrary properties (args).
 */
abstract class FeedItem extends Model
{
    static $table_name = 'feed_items';
    static $table_attributes = array(
        'feed_name' => '',
        'subtype_id' => '',
        'subject_guid' => null,
        'user_guid' => null,
        'args' => '',
        'time_posted' => 0,        
    );   
    static $query_class = 'Query_SelectFeedItem';
    
    function __get($name)
    {
        $res = parent::__get($name);

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
        parent::__set($name, $value);
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
            ->where('subtype_id = ?', $this->get_subtype_id())
            ->where('subject_guid = ?', $this->subject_guid)
            ->where('time_posted = ?', $this->time_posted);
    }

    function is_valid()
    {
        $subject = $this->get_subject_entity();
        if (!$subject)
            return false;

        $user = $this->get_user_entity();
        if (!$user)
            return false;        
        
        return true;
    }

    function render_heading($mode)
    {
        return '';
    }

    function render_content($mode)
    {
        return '';
    }

    function render_thumbnail($mode)
    {
        return '';
    }
    
    protected function get_link($title)
    {
        return "<a href='{$this->get_url()}'>".escape($title)."</a>";
    }
    
    protected function get_user_link($mode)
    {
        $user = $this->get_user_entity();
        
        if ($mode == 'self')
        {
            return escape($user->name);
        }
        else
        {
            return "<a class='feed_org_name' href='{$user->get_url()}'>".escape($user->name)."</a>";
        }
    }
    
    function get_url()
    {
        return $this->get_subject_entity()->get_url();
    }         
    
    static function query_by_feed_names($feedNames)
    {
        $query = static::query();        
        $query->where_in("feed_name", $feedNames);

        if (sizeof($feedNames) > 1)
        {
            $query->group_by('subtype_id, subject_guid, time_posted');
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
    
    static function post($user, $subject, $args = null, $time = null)
    {
        if (!$time)
        {
            $time = timestamp();
        }

        $feedNames = $user->get_feed_names();

        if ($subject != $user && method_exists($subject, 'get_feed_names'))
        {
            $feedNames = $feedNames + $subject->get_feed_names();
            $feedNames = array_flip(array_flip($feedNames));
        }

        foreach ($feedNames as $feedName)
        {
            $cls = get_called_class();
            $feedItem = new $cls();
            $feedItem->feed_name = $feedName;
            $feedItem->subject_guid = $subject->guid;
            $feedItem->user_guid = $user->guid;
            $feedItem->time_posted = $time;
            $feedItem->args = $args;

            $feedItem->save();
        }
    }    
    
    static function feed_name_from_filters($filters)
    {    
        $conditions = array();
        foreach ($filters as $filter)
        {
            $conditions[$filter->get_param_name()] = $filter->value;
        }    

        if (@$conditions['region'] && @$conditions['country'])
        {
            unset($conditions['country']);
        }            
        
        return static::make_feed_name($conditions);
    }
    
    function get_sms_description()
    {
        return null;
    }
}
