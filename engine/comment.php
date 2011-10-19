<?php

/*
 * Represents a comment about a news update (or other entity).
 * Each news update may have multiple associated comments.
 * Comments may be associated with a registered User, or anonymous.
 */
class Comment extends Entity
{
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
    
    function send_notifications($except = null)
    {    
		$org = $this->get_root_container_entity();        
        $owner_guid = $this->owner_guid;		           
        $widget = $this->get_container_entity();            
        
		$notification_subject = sprintf(__('comment:notification_subject', $org->language), 
			$this->get_name());        
		
        $reply_to = EmailAddress::add_signed_tag(Config::get('reply_email'), "comment{$this->guid}");
        
        // notify organization by email (unless the organization posted the comment itself)
		if ($org->email && $org->is_notification_enabled(Notification::Comments) && $owner_guid != $org->guid)
		{		
            $mail = OutgoingMail::create($notification_subject);
            $mail->set_body_html(view('emails/comment_added', array(
                'comment' => $this, 
                'user' => $org,
            )));
            $mail->setReplyTo($reply_to);
			$mail->send_to_user($org);
		}

        // notify site admins by email (unless the organization posted the comment itself)
        if ($owner_guid != $org->guid)
        {
            $mail = OutgoingMail::create(
                sprintf(__('comment:notification_admin_subject'), $this->get_name(), $org->name)
            );
            $mail->setReplyTo($reply_to);
            $mail->set_body_html(view('emails/comment_added', array(
                'comment' => $this, 
            )));        
            $mail->send_to_admin();                                  
        }
        
        // notify sms subscribers (except for the one who posted the comment)
        $phones = array();
        
        $subscriptions = array_merge(
            $org->query_sms_subscriptions()->where('notification_type = ?', Notification::Comments)->filter(),
            $widget->query_sms_subscriptions()->filter()
        );
        
        foreach ($subscriptions as $subscription)
        {                        
            $phone = $subscription->phone_number;
            if ($phone != $except && !isset($phones[$phone]))
            {        
                $phones[$phone] = true;
                
                $subscription->notify(strtr(__('sms:comment_notification', $subscription->language), array(
                    '{name}' => $this->get_name($subscription->language),
                    '{news_cmd}' => "N {$org->username} {$widget->get_local_id()}",
                    '{comment_cmd}' => "V {$this->guid}",
                    '{news_url}' => $widget->get_url(),
                )));                               
            }
        }        
    }
}