<?php

class SMS_Provider_Telerivet extends SMS_Provider
{
    function get_request_from()
    {        
        return $_POST['from_number'];
    }
    
    function get_request_to()
    {        
        return $_POST['to_number'];
    }    
    
    function get_request_text()
    {
        return $_POST['content'];
    }

    function render_response($replies, $controller)
    {
        $controller->set_content_type('application/json');
        
        $messages = array();
        
        foreach ($replies as $reply)
        {
            $messages[] = array('content' => $reply);
        }
        
        $controller->set_content(json_encode(array('messages' => $messages)));
    }
        
    function is_validated_request()
    {
        return Config::get('telerivet:webhook_secret') == $_POST['secret'];
    }    
    
    static function format_number($phone_number)
    {
        return $phone_number;
    }
    
    function can_send_sms()
    {
        return true;
    }
    
    function send_sms($sms)
    {
        $api_key = Config::get('telerivet:api_key');
        $api_url = Config::get('telerivet:api_url');
        $project_id = Config::get('telerivet:project_id');
                
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "{$api_url}/v1/projects/{$project_id}/messages/outgoing");
        curl_setopt($curl, CURLOPT_USERPWD, "{$api_key}:");  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);              
        curl_setopt($curl, CURLOPT_CAINFO, Engine::$root.'/vendors/cacert.pem');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
            'content' => $sms->message,
            'from_number' => $sms->from_number,
            'to_number' => $sms->to_number
        )));    
        $json = curl_exec($curl);
        $info = curl_getinfo($curl);
        $error = curl_error($curl);
        curl_close($curl);
        
        if ($error)
        {
            throw new IOException("Error sending SMS via Telerivet: $error");
        }        
        else if ($info['http_code'] != 200)
        {
            throw new IOException("Error sending SMS via Telerivet: HTTP {$info['http_code']}");
        }
    }    
}