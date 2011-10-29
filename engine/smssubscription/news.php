<?php

class SMSSubscription_News extends SMSSubscription
{
    function send_notification($event_name, $post)
    {        
        $org = $post->get_container_user();
        
        $sender_phone = $post->get_metadata('phone_number');        
        if ($this->phone_number == $sender_phone)
        {
            return;
        }
        
        if ($event_name == Widget::Added)
        {            
            $this->send(array(
                'notifier' => $post,
                'message' => strtr(__('sms:post_notification', $this->language), array(
                    '{username}' => $org->username,
                    '{cmd}' => "N {$org->username} {$post->get_local_id()}",
                    '{url}' => $post->get_container_entity()->get_url(),
                ))
            ));
        }
    }
    
    function get_description()
    {
        $user = $this->get_container_user();          
        return "N {$user->username}";
    }
}
