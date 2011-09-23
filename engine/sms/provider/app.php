<?php

class SMS_Provider_App extends SMS_Provider
{   
    public $request;

    function __construct()
    {
        require_once(Config::get('root').'/vendors/EnvayaSMS.php');
        $this->request = EnvayaSMS::get_request();
    }

    // only valid for EnvayaSMS_Action_Incoming
    
    function get_request_from()
    {   
        return $this->request->get_action()->from;
    }
    
    function get_request_to()
    {        
        return $this->request->get_phone_number();        
    }          
    
    function get_request_message()
    {
        return $this->request->get_action()->message;
    }
    
    function is_validated_request()
    {
        $phone_number = $this->request->get_phone_number();
                                
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
        
        return $this->request->is_validated($secret);
    }    
    
    function render_response($replies, $controller)
    {
        $messages = array();
        
        foreach ($replies as $reply)
        {        
            $message = new EnvayaSMS_OutgoingMessage();
            $message->message = $reply;
            $messages[] = $message;
        }
        
        error_log(var_export($messages, true));
        
        $controller->set_content_type('text/xml');
        $controller->set_content($this->request->get_action()->get_response_xml($messages));
    }
    
    function can_send_sms()
    {
        return false;
    }    
}