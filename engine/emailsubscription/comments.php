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
    
    static function handle_mail_reply($mail, $match)
    {
        $guid = $match['guid'];
        
        $comment = Comment::get_by_guid($guid, true);
        if (!$comment)
        {
            error_log("invalid comment guid $guid");
            return false;
        }
        
        $widget = $comment->get_container_entity();
        if (!$widget)
        {
            error_log("invalid container for comment guid $guid");
            return false;
        }
        
        $parsed_address = EmailAddress::parse_address($mail->from);
        
        $reply = new Comment();
        $reply->container_guid = $widget->guid;    
        $reply->name = @$parsed_address['name'];
        $reply->location = "via email";
        $reply->set_content(nl2br(escape(IncomingMail::strip_quoted_text($mail->text))), true);
        $reply->save();
        
        $widget->refresh_attributes();
		$widget->save();
        
        error_log("added comment {$reply->guid}");
        
        return true;
    }            
}
