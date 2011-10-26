<?php

class SMSTemplate extends ContactTemplate
{
    static $table_name = 'sms_templates';    
    static $outgoing_message_class = 'OutgoingSMS';
    static $subscription_class = 'SMSSubscription_Contact';
    
    static $count_filters_url = '/admin/contact/sms/filters_count';
        
    function get_url()
    {
        return "/admin/contact/sms/{$this->guid}";
    }    
    
    function get_description()
    {
        return Markup::truncate_at_word_boundary($this->content, 60);
    }
}