<?php

class SMS_Provider_Twilio extends SMS_Provider
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
    
    function send_sms($from, $to, $msg)
    {
        $from = static::format_number($from);
        $to = static::format_number($to);   
    
        $twilio = static::get_client();    
        $twilio->account->sms_messages->create(
            $from,
            $to, 
            $msg
        );    
    }    
    
    function is_validated_request()
    {
        static::load_lib();
        
        $validator = new Services_Twilio_RequestValidator(Config::get('twilio_auth_token'));
        $url = Request::full_original_url();        
        $expected_signature = @$_SERVER["HTTP_X_TWILIO_SIGNATURE"];
        return $validator->validate($expected_signature, $url, $_POST);
    }    
    
    static function format_number($phone_number)
    {
        $phone_number = preg_replace('/[^\d]/','', $phone_number);
        return "+$phone_number";
    }    
}