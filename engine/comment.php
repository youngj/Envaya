<?php

/*
 * Represents a comment about a news update (or other Widget).
 * Each news update may have multiple associated comments.
 * Comments may be associated with a registered User, or anonymous.
 */
class Comment extends Entity
{
    // event names
    const Added = 'added';

    static $table_name = 'comments';
    static $table_attributes = array(
        'name' => '',
        'location' => '',
        'email' => '',
    );   
    static $mixin_classes = array(
        'Mixin_Content'
    );        
    
    function get_url()
    {
        $widget = $this->get_container_entity();
        return "{$widget->get_url()}?comments=1#comment{$this->guid}";
    }
    
    function get_base_url()
    {
        $widget = $this->get_container_entity();
        return "{$widget->get_url()}/comment/{$this->guid}";
    }
    
	function get_name($lang = null)
	{
        if ($this->name)
        {
            return $this->name;
        }        
        
        $owner = $this->get_owner_entity();        
        if ($owner)
		{
			return $owner->name;
		}

        return "(".__('comment:anonymous', $lang).")";
	}    
    
    function is_session_owner()
    {               
        $posted_comments = Session::get('posted_comments') ?: array();
        return in_array($this->guid, $posted_comments);
    }   
    
    function set_session_owner()
    {
        $posted_comments = Session::get('posted_comments') ?: array();
        $posted_comments[] = $this->guid;
        Session::set('posted_comments', $posted_comments);    
    }
        
    function send_notifications($event_name)
    {
        EmailSubscription_Comments::send_notifications($event_name, $this);
        SMSSubscription_Comments::send_notifications($event_name, $this);    
    }       
}