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
        return $this->request->phone_number;        
    }          
    
    function get_request_text()
    {
        return $this->request->get_action()->message;
    }
    
    function render_message_html()
    {
        $html = parent::render_message_html();
        
        $action = $this->request->get_action();
        
        if ($action->mms_parts)
        {        
            foreach ($action->mms_parts as $mms_part)
            {
                if (strpos($mms_part->type, 'image/') === 0)
                {
                    $uploaded_file = UploadedFile::upload_from_input($_FILES[$mms_part->form_name]);
                    
                    $url = $uploaded_file->get_url();
                    
                    $html .= " <br /><img src='$url' class='image_center' />";
                }
            }
        }
        return $html;
    }    
    
    function is_validated_request()
    {
        $phone_number = $this->request->phone_number;
                                
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
    
    function get_log_line()
    {
        return parent::get_log_line() . " v{$this->request->version}";        
    }    
    
    function can_send_sms()
    {
        return false;
    }    
}