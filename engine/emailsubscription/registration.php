<?php

class EmailSubscription_Registration extends EmailSubscription
{
    function send_notification($event_name, $user)
    {
        $url = secure_url($user->get_url());
        $link = "<a href='$url'>$url</a>";
    
        if ($event_name == Organization::Registered)
        {
            $this->send(array(
                'notifier' => $user,
                'subject' => sprintf(__('email:registernotify:subject'), $user->name), 
                'body' => sprintf(__('email:registernotify:body'), $link),
            ));
        }
        else if ($event_name == Person::Registered)
        {
            $this->send(array(
                'notifier' => $user,
                'subject' => sprintf(__('register:notification_subject'), $user->name), 
                'body' => $link,
            ));
        }
    }
    
    function get_description()
    {
        $container = $this->get_container_entity();               
        if ($container instanceof UserScope)
        {
            return strtr(__('register:scope_subscription'), array('{scope}' => $container->get_title()));
        }
        return '?';
    }
}
