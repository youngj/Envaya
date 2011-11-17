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

        $sid = Config::get('sms:twilio_account_sid');
        $token = Config::get('sms:twilio_auth_token');
        
        return new Services_Twilio($sid, $token, '2010-04-01', $http);
    }       
    
    static function load_lib()
    {
        require_once Engine::$root."/vendors/Services/Twilio.php";
    }
    
    function get_request_from()
    {        
        return @$_POST['From'];
    }
    
    function get_request_to()
    {        
        return @$_GET['to'];
    }    
    
    function get_request_text()
    {
        return @$_POST['Body'];
    }

    function render_response($replies, $controller)
    {
        $controller->set_content_type('text/xml');
    
        ob_start();
        echo "<?xml version='1.0' encoding='UTF-8'?>\n";
        echo '<Response>';
        foreach ($replies as $reply)
        {
            echo "<Sms>".escape($reply)."</Sms>";
        }
        echo '</Response>';  
        $xml = ob_get_clean();    
        
        $controller->set_content($xml);
    }
        
    function is_validated_request()
    {
        static::load_lib();
        
        $validator = new Services_Twilio_RequestValidator(Config::get('sms:twilio_auth_token'));
        $url = Request::full_original_url();        
        $expected_signature = @$_SERVER["HTTP_X_TWILIO_SIGNATURE"];
        return $validator->validate($expected_signature, $url, $_POST);
    }    
    
    static function format_number($phone_number)
    {
        $phone_number = preg_replace('/[^\d]/','', $phone_number);
        return "+$phone_number";
    }
    
    function can_send_sms()
    {
        return true;
    }
    
    function send_sms($sms)
    {
        $from = static::format_number($sms->from_number);
        $to = static::format_number($sms->to_number);   
    
        $twilio = static::get_client();    
        $twilio->account->sms_messages->create(
            $from,
            $to, 
            $sms->message
        );    
    }    
}