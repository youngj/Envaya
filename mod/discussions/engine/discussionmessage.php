<?php

class DiscussionMessage extends Entity
{
    // event names
    const Added = 'newmessage';   
    const NewTopic = 'newtopic';

    static $table_name = 'discussion_messages';
    
    static $table_attributes = array(
        'message_id' => '',
        'subject' => '',        
        'from_name' => '',
        'from_location' => '',
        'from_email' => '',
        'time_posted' => 0,
        'status' => 1,
    );
    
    static $mixin_classes = array(
        'Mixin_Content'
    );
    
    const Published = 1;
    const Deleted = 0;
    
    function get_text_content()
    {
        return Markup::sanitize_html($this->content, array(
            'AutoFormat.Linkify' => false,
            'HTML.AllowedElements' => ''
        ));    
    }
    
    function get_base_url()
    {
        $topic = $this->get_container_entity();
        return "{$topic->get_url()}/message/{$this->guid}";
    }
    
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
    
    function get_date_text($time = null)
    {
        if (!$time)
        {        
            $time = $this->time_posted;
        }
    
        $org = $this->get_container_user();    
        return get_date_text($time, array(
            'timezoneID' => $org->get_timezone_id(),
            'showTimeZone' => true,
            'showTime' => true
        ));
    }
    
    function post_feed_items()
    {
        $owner = $this->get_owner_entity();
        
        if ($owner && $owner->is_approved())
        {    
            $org = $this->get_container_user();    
            FeedItem_Message::post($org, $this);
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

    function is_session_owner()
    {       
        $posted_messages = Session::get('posted_messages') ?: array();
        return in_array($this->guid, $posted_messages);
    }
    
    function set_session_owner()
    {
        $posted_messages = Session::get('posted_messages') ?: array();
        $posted_messages[] = $this->guid;
        Session::set('posted_messages', $posted_messages);    
    }
    
    function send_notifications($event_name)
    {
        EmailSubscription_Discussion::send_notifications($event_name, $this);
    }
}