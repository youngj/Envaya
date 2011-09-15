<?php

class SMS_Provider_KalSMS extends SMS_Provider
{   
    public $kalsms;

    function __construct()
    {
        require_once(Config::get('root').'/vendors/kalsms.php');
        $this->kalsms = KalSMS::new_from_request();
    }

    // only valid for KalSMS_Action_Incoming
    
    function get_request_from()
    {   
        return $this->kalsms->get_request_action()->from;
    }
    
    function get_request_to()
    {        
        return $this->kalsms->get_request_phone_number();        
    }          
    
    function get_request_message()
    {
        return $this->kalsms->get_request_action()->message;
    }
    
    function is_validated_request()
    {
        $phone_number = $this->kalsms->get_request_phone_number();
                                
        if (!$phone_number)
        {
            return false;
        }
        
        $secrets = Config::get('sms_gateway_secrets');
        $secret = @$secrets[$phone_number];
        if (!$secret)
        {            
            return false;
        }
        
        return $this->kalsms->is_validated_request($secret);
    }    
    
    function render_response($replies, $controller)
    {
        $messages = array();
        
        foreach ($replies as $reply)
        {        
            $message = new KalSMS_OutgoingMessage();
            $message->message = $reply;
            $messages[] = $message;
        }
        
        error_log(var_export($messages, true));
        
        $controller->set_content_type('text/xml');
        $controller->set_content($this->kalsms->get_request_action()->get_response_xml($messages));
    }
    
    function can_send_sms()
    {
        return false;
    }    
}