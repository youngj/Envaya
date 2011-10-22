<?php

/*
 * Represents a comment about a news update (or other entity).
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
    );   
    static $mixin_classes = array(
        'Mixin_Content'
    );        
    
	function get_name($lang = null)
	{
		$owner = $this->get_owner_entity();
		if ($owner)
		{
			return $owner->name;
		}
		else
		{
			return $this->name ?: "(".__('comment:anonymous', $lang).")";
		}
	}
    
    function can_edit()
    {           
        return parent::can_edit() || $this->is_session_owner();
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
		$org = $this->get_root_container_entity();        
        $widget = $this->get_container_entity();                   
        
        $email_subscriptions = EmailSubscription::merge(
            EmailSubscription_Comments::query_for_entity($org)->filter(),
            EmailSubscription_Comments::query_for_entity($widget)->filter()
        );        
        
        foreach ($email_subscriptions as $subscription)
        {
            $subscription->send_notification($event_name, $this);
        }
        
        $sms_subscriptions = SMSSubscription::merge(
            SMSSubscription_Comments::query_for_entity($org)->filter(),
            SMSSubscription_Comments::query_for_entity($widget)->filter()
        );
        
        foreach ($sms_subscriptions as $subscription)
        {
            $subscription->send_notification($event_name, $this);
        }
    }
}