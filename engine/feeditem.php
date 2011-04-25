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
 * action_name refers to a FeedItem subclass defined in engine/feeditem/
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
    
    static function new_from_row($row)
    {
        $cls = "FeedItem_{$row->action_name}";
        if (class_exists($cls))
        {
            return new $cls($row);
        }
        else
        {
            return new FeedItem_Invalid($row);
        }
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

    function is_valid()
    {
        $subject = $this->get_subject_entity();
        if (!$subject || !$subject->is_enabled())
            return false;

        $user = $this->get_user_entity();
        if (!$user || !$user->is_enabled())
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
    
    protected function get_org_link($mode)
    {
        $org = $this->get_user_entity();
        
        if ($mode == 'self')
        {
            return escape($org->name);
        }
        else
        {
            return "<a class='feed_org_name' href='{$org->get_url()}'>".escape($org->name)."</a>";
        }
    }
    
    function get_url()
    {
        return $this->get_subject_entity()->get_url();
    }         

    static function get_action_name()    
    {
        return substr(get_called_class(), 9 /* strlen(FeedItem_) */ );
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
    
    static function post($user, $subject, $args = null, $time = null)
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
            $feedItem->action_name = static::get_action_name();
            $feedItem->subject_guid = $subject->guid;
            $feedItem->user_guid = $user->guid;
            $feedItem->time_posted = $time;
            $feedItem->args = $args;

            $feedItem->save();
        }
    }    
}