<?php

class EmailSubscription_Discussion extends EmailSubscription
{
    static function handle_mail_reply($mail, $match)
    {
        $tid = $match['tid'];
        
        $message = DiscussionMessage::query()->where('tid = ?', $tid)->show_disabled(true)->get();
        if (!$message)
        {
            error_log("invalid message tid $tid");
            return false;
        }
                
        $topic = $message->get_container_entity();
        if (!$topic)
        {
            error_log("invalid container for message tid $tid");
            return false;
        }
        
        $parsed_address = EmailAddress::parse_address($mail->from);
        
        $reply = new DiscussionMessage();
        $reply->container_guid = $topic->guid;    
        $reply->from_name = @$parsed_address['name'];
        $reply->subject = $mail->subject;
        $reply->from_location = "via email";
        $reply->from_email = @$parsed_address['address'];
        $reply->set_content(nl2br(IncomingMail::strip_quoted_text($mail->text)));
        $reply->time_posted = timestamp();
        $reply->save();        

        $topic->refresh_attributes();
		$topic->save();
        
        $reply->send_notifications(DiscussionMessage::Added);
        
        return true;
    }
    
    function send_notification($event_name, $message)
    {
        $org = $message->get_container_user();
    
        if ($message->owner_guid && $message->owner_guid == $this->owner_guid)
        {        
            return;
        }
        
        if ($message->from_email == $this->email)
        {
            return;
        }        
                
        $tr = array(
            '{name}' => $message->from_name
        );
    
        if ($event_name == DiscussionMessage::Added)
        {
            $subject = strtr(__('discussions:notification_subject', $this->language), $tr);
        }
        else if ($event_name == DiscussionMessage::NewTopic)
        {
            $subject = strtr(__('discussions:notification_topic_subject', $this->language), $tr); 
        }
        else
        {
            $subject = "?";
        }
                
        $this->send(array(
            'notifier' => $message,
            'subject' => $subject,
            'body' => view('emails/discussion_message', array(
                'message' => $message,
            )),
            'reply_to' => EmailAddress::add_signed_tag(Config::get('mail:reply_email'), "message{$message->tid}"),
        ));
    }
    
    function get_description()
    {
        $container = $this->get_container_entity();      
        
        if ($container instanceof DiscussionTopic)
        {
            return strtr(__('discussions:topic_subscription'), array('{topic}' => $container->subject));
        }
        else if ($container instanceof User)
        {
            return strtr(__('discussions:user_subscription'), array('{name}' => $container->name));
        }        
        else if ($container instanceof UserScope)
        {
            return strtr(__('discussions:scope_subscription'), array('{scope}' => $container->get_title()));            
        }
        return '?';
    }
}