<?php

class EmailSubscription_Contact extends EmailSubscription
{
    static $query_subtype_ids = array('contact.subscription.email.contact');

    function send_notification($event_name, $template)
    {
        $this->send(array(
            'notifier' => $template,
            'from_name' => $template->from,
            'subject' => $template->render_subject($this),
            'body' => $template->render_content($this),
        ));    
    }
    
    function get_description()
    {
        $user = $this->get_root_container_entity();    
        $tr = array('{name}' => $user->name);
        return strtr(__('contact:subscription'), $tr);
    }
}
