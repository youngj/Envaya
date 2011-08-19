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
    
	function get_name()
	{
		$owner = $this->get_owner_entity();
		if ($owner)
		{
			return $owner->name;
		}
		else
		{
			return $this->name ?: "(".__('comment:anonymous').")";
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
    
    function send_notifications()
    {    
		$org = $this->get_root_container_entity();        
        $owner_guid = $this->owner_guid;
		
		$notification_subject = sprintf(__('comment:notification_subject', $org->language), 
			$this->get_name());
            
        $widget = $this->get_container_entity();            
        $comments_url = $widget->get_url()."?comments=1";            
            
		$notification_body = view('emails/comment_added', array(
            'comment' => $this, 
            'url' => "$comments_url#comments",
        ));
		
        $reply_to = EmailAddress::add_signed_tag(Config::get('reply_email'), "comment{$this->guid}");
        
		if ($org && $org->email && $org->is_notification_enabled(Notification::Comments) && $owner_guid != $org->guid)
		{		
            $mail = OutgoingMail::create($notification_subject, $notification_body);
            $mail->setReplyTo($reply_to);
			$mail->send_to_user($org);
		}        

        $mail = OutgoingMail::create(
            sprintf(__('comment:notification_admin_subject'), $this->get_name(), $org->name),
            $notification_body
        );
        $mail->setReplyTo($reply_to);
        $mail->send_to_admin();    
    }
}
