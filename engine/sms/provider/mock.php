<?php

class SMS_Provider_Mock extends SMS_Provider
{
    function send_sms($from, $to, $msg)
    {
        $file = fopen(Config::get('mock_sms_file'), 'a');
        if (!$file)
        {
            return;
        }
        fwrite($file, "========\n");
        fwrite($file, "From: $from\n");
        fwrite($file, "To: $to\n");
        fwrite($file, "Message: $msg\n");
        fwrite($file, "--------\n");
        fclose($file);
    }    
    
    function init_request()
    {
        $twilio = new SMS_Provider_Twilio();
        return $twilio->init_request();
    }
    
    function is_validated_request()
    {
        return true;
    }    
}