<?php

class SMSTemplate extends ContactTemplate
{
    static $table_name = 'sms_templates';    
    static $outgoing_message_class = 'OutgoingSMS';
    static $subscription_class = 'SMSSubscription_Contact';
        
    function get_url()
    {
        return "/admin/contact/sms/{$this->guid}";
    }    
}