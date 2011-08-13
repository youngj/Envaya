<?php

class Twilio
{
    static function get_client()
    {
        static::load_lib();

        // https://github.com/twilio/twilio-php/issues/18
        $http = new Services_Twilio_TinyHttp('https://api.twilio.com', array('curlopts' => array(
            CURLOPT_SSL_VERIFYPEER => false
        )));

        $sid = Config::get('twilio_account_sid');
        $token = Config::get('twilio_auth_token');
        
        return new Services_Twilio($sid, $token, '2010-04-01', $http);
    }       
    
    static function load_lib()
    {
        require_once Config::get('root')."/vendors/Services/Twilio.php";
    }
    
    static function is_validated_request()
    {
        static::load_lib();
        
        $validator = new Services_Twilio_RequestValidator(Config::get('twilio_auth_token'));
        $url = Request::full_original_url();        
        $expected_signature = @$_SERVER["HTTP_X_TWILIO_SIGNATURE"];
        return $validator->validate($expected_signature, $url, $_POST);
    }
    
    static function send_sms_now($from, $to, $msg)
    {
        if ($from[0] != '+')
        {
            $from = "+$from";
        }    
    
        if ($to[0] != '+')
        {
            $to = "+$to";
        }
    
        $twilio = static::get_client();    
        $twilio->account->sms_messages->create(
            $from,
            $to, 
            $msg
        );    
    }
    
    static function send_sms($from, $to, $msg)
    {
        FunctionQueue::queue_call(
            array('Twilio', 'send_sms_now'), array($from, $to, $msg),
            FunctionQueue::LowPriority
        );
    }
}