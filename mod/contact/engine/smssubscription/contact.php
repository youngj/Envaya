<?php

class SMSSubscription_Contact extends SMSSubscription
{
    function send_notification($event_name, $template)
    {
        $this->send(array(
            'notifier' => $template,
            'message' => $template->render_content($this),
            'append_stop' => false,
        )); 
    }    
    
    function get_description()
    {
        return "admin msg";
    }
}
