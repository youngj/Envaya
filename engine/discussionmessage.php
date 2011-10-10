<?php

class DiscussionMessage extends Entity
{
    static $table_name = 'discussion_messages';
    
    static $table_attributes = array(
        'message_id' => '',
        'subject' => '',        
        'from_name' => '',
        'from_location' => '',
        'from_email' => '',
        'time_posted' => 0,
    );
    static $mixin_classes = array(
        'Mixin_Content'
    );
    
    function get_text_content()
    {
        return Markup::sanitize_html($this->content, array(
            'AutoFormat.Linkify' => false,
            'HTML.AllowedElements' => ''
        ));    
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
    
    function get_date_text()
    {
        $org = $this->get_root_container_entity();    
        return get_date_text($this->time_posted, array(
            'timezoneID' => $org->get_timezone_id(),
            'showTime' => true
        ));
    }
    
    function post_feed_items()
    {
        $owner = $this->get_owner_entity();
        
        if ($owner && $owner->is_approved())
        {    
            $org = $this->get_root_container_entity();    
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

    function can_edit()
    {           
        return parent::can_edit() || $this->is_session_owner();
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
    
    function send_notifications($is_new_topic = false)
    {
        $org = $this->get_root_container_entity();
        $user = $this->get_owner_entity();
    
        $reply_to = EmailAddress::add_signed_tag(Config::get('reply_email'), "message{$this->guid}");
    
        if ($org->is_notification_enabled(Notification::Discussion)
            && (!$user || $user->guid != $org->guid))
        {
            $subject = strtr(($is_new_topic ? 
                    __('discussions:notification_topic_subject', $org->language) : 
                    __('discussions:notification_subject', $org->language)
                ), 
                array(
                    '{name}' => $this->from_name
                )
            );
        
            // notify site of message
            $mail = OutgoingMail::create($subject);
            $mail->setReplyTo($reply_to);
            $mail->set_body_html(view('emails/discussion_message', array(
                'message' => $this,
                'user' => $org,
            )));
            $mail->send_to_user($org);
        }
    }    
}