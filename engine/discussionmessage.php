<?php

class DiscussionMessage extends Entity
{
    static $table_name = 'discussion_messages';
    
    static $table_attributes = array(
        'message_id' => '',
        'list_guid' => 0,
        'subject' => '',        
        'from_name' => '',
        'from_email' => '',
        'time_posted' => 0,
        
        'content' => '',
        'data_types' => 0,        
        'language' => '',
    );
    
    function get_url()
    {
        $topic = $this->get_container_entity();
        $url = $topic->get_url();
        
        if ($this->guid != $topic->first_message)
        {
            $url .= "#msg{$this->guid}";
        }        
        
        return $url;        
    }
    
    function get_date_text()
    {
        $org = $this->get_root_container_entity();    
        return get_date_text($this->time_posted, array(
            'timezone_id' => $org->get_timezone_id(),
            'show_time' => true
        ));
    }
    
    function post_feed_items()
    {
        $owner = $this->get_owner_entity();
        
        if ($owner && $owner->is_approved())
        {    
            $org = $this->get_root_container_entity();    
            post_feed_items($org, 'message', $this);
        }
    }
    
    function get_from_link()
    {
        $name = escape($this->from_name);
            
        $owner = $this->get_owner_entity();
        
        if ($owner && $owner instanceof Organization)
        {
            return "<a href='{$owner->get_url()}'>$name</a>";
        }
        return $name;    
    }
}