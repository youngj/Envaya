<?php

class EmailSubscription_Discussion extends EmailSubscription
{
    static $query_subtype_ids = array('core.subscription.email.discussion');

    function send_notification($event_name, $message)
    {
        $org = $message->get_root_container_entity();
    
        if ($message->owner_guid && $message->owner_guid == $this->owner_guid)
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
            'reply_to' => EmailAddress::add_signed_tag(Config::get('reply_email'), "message{$message->guid}"),
        ));
    }
    
    function get_description()
    {
        $user = $this->get_root_container_entity();    
        $tr = array('{name}' => $user->name);
        return strtr(__('email:subscribe_discussion'), $tr);
    }
}
