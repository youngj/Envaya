<?php

class SMSSubscription_Contact extends SMSSubscription
{
    static $query_subtype_ids = array('contact.subscription.sms.contact');

    function send_notification($event_name, $template)
    {
        $this->send(array(
            'notifier' => $template,
            'message' => $template->render_content($this),
        )); 
    }    
    
    function get_description()
    {
        return "admin msg";
    }
}
