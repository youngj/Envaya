<?php

/*
 * A template for an email message that can be sent to multiple users.
 */
class EmailTemplate extends ContactTemplate
{
    static $table_name = 'email_templates';
    static $outgoing_message_class = 'OutgoingMail';
    static $subscription_class = 'EmailSubscription_Contact';
    
    static $count_filters_url = '/admin/contact/email/filters_count';
    
    static $table_attributes = array(
        'subject' => '',
        'from' => '',
        'num_sent' => 0,
        'time_last_sent' => 0,
        'filters_json' => '',
    );    
    
    function render_subject($subscription)
    {
        return $this->render($this->subject, $subscription);
    }    
    
    function get_url()
    {
        return "/admin/contact/email/{$this->guid}";
    }
    
    function get_description()
    {
        return $this->subject ?: '(No Subject)';
    }    
}