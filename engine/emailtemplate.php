<?php

/*
 * A template for an email message that can be sent to multiple users.
 * The message can contain {}-delimited strings with properties of a User or Organization,
 * (e.g. {username}) which will be replaced with the appropriate values for each user.
 */

class EmailTemplate extends Entity
{
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
        return ($org && $org->email && $org->is_notification_enabled(Notification::Batch)        
            && OutgoingMail::query()
                ->where('email_guid = ?', $this->guid)
                ->where('user_guid = ?', $org->guid)
                ->where('status <> ?', OutgoingMail::Failed)
                ->count() == 0
        );
    }
    
    function send_to($org)
    {        
        $subject = $this->render_subject($org);
        $body = view('emails/template', array('org' => $org, 'email' => $this));

        $mail = OutgoingMail::create($subject);
        $mail->setBodyHtml($body);
        $mail->setFrom(Config::get('email_from'), $this->from);
        $mail->email_guid = $this->guid;
        $mail->send_to_user($org);
 
        $org->last_notify_time = time();
        $org->save();
    }
}