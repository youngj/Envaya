<?php

class SMS_Provider_Mock extends SMS_Provider
{
    function can_send_sms()
    {
        return true;
    }   

    function send_sms($sms)
    {
        $file = fopen(Config::get('mock_sms_file'), 'a');
        if (!$file)
        {
            return;
        }
        fwrite($file, "========\n");
        fwrite($file, "From: {$sms->from_number}\n");
        fwrite($file, "To: {$sms->to_number}\n");
        fwrite($file, "Message: {$sms->message}\n");
        fwrite($file, "--------\n");
        fclose($file);
    }    
    
    function get_request_from()
    {        
        return @$_GET['from'];
    }
    
    function get_request_to()
    {        
        return @$_GET['to'];
    }    
    
    function get_request_text()
    {
        return @$_GET['msg'];
    }    
    
    function render_response($replies, $controller)
    {
        $twilio = new SMS_Provider_Twilio();
        $twilio->render_response($replies, $controller);
    }    
    
    function is_validated_request()
    {
        return true;
    }    
}