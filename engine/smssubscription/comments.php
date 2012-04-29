<?php

class SMSSubscription_Comments extends SMSSubscription
{
    function send_notification($event_name, $comment)
    {        
        $sender_phone = $comment->get_metadata('phone_number');    
        if ($this->phone_number == $sender_phone)
        {        
            return;
        }
        
        if ($comment->owner_guid && $comment->owner_guid === $this->owner_guid)
        {        
            return;
        }
        
        $org = $comment->get_container_user();        
        $widget = $comment->get_container_entity();            
    
        if ($event_name == Comment::Added)
        {        
            $this->send(array(
                'notifier' => $comment,
                'message' => strtr(__('sms:comment_notification', $this->language), array(
                    '{name}' => $comment->get_name($this->language),
                    '{news_cmd}' => "N {$org->username} {$widget->local_id}",
                    '{comment_cmd}' => "V {$comment->guid}",
                    '{news_url}' => abs_url($widget->get_url()),
                ))
            ));
        }
    }
    
    function get_description()
    {
        $user = $this->get_container_user();            
        $container = $this->get_container_entity();        
        
        if ($container instanceof Widget)
        {
            return "N {$user->username} {$container->local_id}";
        }
        else
        {
            return "G {$user->username}";
        }
    }
}
