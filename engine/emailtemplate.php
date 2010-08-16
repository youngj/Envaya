<?php

class EmailTemplate extends ElggObject
{
    static $subtype_id = T_email_template;
    static $table_name = 'email_templates';

    static $table_attributes = array(
        'subject' => '',
        'content' => '',
        'from' => '',
        'data_types' => 0,
        'language' => '',
        'active' => 0,
        
    );
    
    function render_content($org)
    {
        if ($org)
        {
            return $org->render_email_template($this->content);
        }
        else
        {
            return $this->content;
        }
    }
    
    function render_subject($org)
    {
        if ($org)
        {
            return $org->render_email_template($this->subject);
        }
        else
        {
            return $this->subject;
        }
    }
    
    function can_send_to($org)
    {
        return ($org && $org->email && $org->notify_days > 0 && $org->approval > 0
            && (!$org->last_notify_time || $org->last_notify_time + $org->notify_days * 86400 < time()));
    }
    
    function send_to($org)
    {        
        $subject = $this->render_subject($org);
        $body = view('emails/template', array('org' => $org, 'email' => $this));

        global $CONFIG;
        
        $headers = array(
            'To' => $org->getNameForEmail(),
            'Content-Type' => 'text/html',
            'From' => "\"{$this->from}\" <{$CONFIG->email_from}>"
        );

        send_mail($org->email, $subject, $body, $headers);
 
        $org->last_notify_time = time();
        $org->save();
    }
}