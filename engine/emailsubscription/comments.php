<?php

class EmailSubscription_Comments extends EmailSubscription
{
    static $query_subtype_ids = array('core.subscription.email.comments');

    function send_notification($event_name, $comment)
    {
        if ($comment->owner_guid && $comment->owner_guid == $this->owner_guid)    
        {
            return;
        }
        
        if ($event_name == Comment::Added)
        {
            $this->send(array(
                'notifier' => $comment,
                'reply_to' => EmailAddress::add_signed_tag(Config::get('reply_email'), "comment{$comment->guid}"),
                'subject' => strtr(__('comment:notification_subject', $this->language), array(
                    '{name}' => $comment->get_name(),
                )), 
                'body' => view('emails/comment_added', array(
                    'comment' => $comment, 
                )),                    
            ));
        }
    }
    
    function get_description()
    {
        $user = $this->get_root_container_entity();    
        $tr = array('{name}' => $user->name);
        return strtr(__('email:subscribe_comments'), $tr);
    }
}
