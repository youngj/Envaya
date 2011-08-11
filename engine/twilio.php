<?php

class Twilio
{
    protected $client;

    function __construct()
    {
        static::load_lib();

        // https://github.com/twilio/twilio-php/issues/18
        $http = new Services_Twilio_TinyHttp('https://api.twilio.com', array('curlopts' => array(
            CURLOPT_SSL_VERIFYPEER => false
        )));

        $sid = Config::get('twilio_account_sid');
        $token = Config::get('twilio_auth_token');
        
        $this->client = new Services_Twilio($sid, $token, '2010-04-01', $http);
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
    
    function send_sms($to, $msg)
    {
        $this->client->account->sms_messages->create(
            Config::get('twilio_phone_number'),
            $to, 
            $msg
        );
    }
}