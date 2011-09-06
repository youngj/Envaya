<?php

class SMS_Provider_GatewayApp extends SMS_Provider
{   
    function get_request_from()
    {        
        return @$_POST['from'];
    }
    
    function get_request_message()
    {
        return @$_POST['message'];
    }
    
    function is_validated_request()
    {
        // todo request signature
        $secrets = Config::get('sms_gateway_secrets');
        $secret = $secrets[$_POST['to']];
        return $secret && @$_POST['secret'] === $secret;
    }    
    
    function render_response($replies, $controller)
    {
        $twilio = new SMS_Provider_Twilio();
        $twilio->render_response($replies, $controller);
    }

    function is_validated_dequeue_request()
    {
        // todo request signature
        $secrets = Config::get('sms_gateway_secrets');
        $secret = $secrets[$_POST['from']];
        return $secret && @$_POST['secret'] === $secret;
    }    
    
    
    function can_send_sms()
    {
        return false;
    }    
}