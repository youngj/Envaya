<?php

class EmailSubscription_Contact extends EmailSubscription
{
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
        $user = $this->get_container_user();    
        $tr = array('{name}' => $user->name);
        return strtr(__('contact:subscription'), $tr);
    }
}
