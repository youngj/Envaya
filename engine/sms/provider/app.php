<?php

class SMS_Provider_App extends SMS_Provider
{   
    private $log_suffix;
    
    function __construct()
    {
        require_once(Engine::$root.'/vendors/EnvayaSMS.php');
    }
    
    
    // only valid for EnvayaSMS_Action_Incoming
    
    function get_request_from()
    {   
        return EnvayaSMS::get_request()->get_action()->from;
    }
    
    function get_request_to()
    {        
        return EnvayaSMS::get_request()->phone_number;        
    }          
    
    function get_request_text()
    {
        return EnvayaSMS::get_request()->get_action()->message;
    }
    
    function render_message_html()
    {
        $html = parent::render_message_html();
        
        $action = EnvayaSMS::get_request()->get_action();
        
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
        $phone_number = EnvayaSMS::get_request()->phone_number;
                                
        if (!$phone_number)
        {
            return false;
        }
        
        $gateways = Config::get('sms:gateways');
        $password = @$gateways[$phone_number]['password'];
        if (!$password)
        {            
            return false;
        }
        
        return EnvayaSMS::get_request()->is_validated($password);
    }    
    
    function get_outgoing_messages()
    {
        $messages = array();
    
        foreach (OutgoingSMS::query()
            ->where('status = ?', OutgoingSMS::Queued)
            ->where('from_number = ?', EnvayaSMS::get_request()->phone_number)
            ->where('time_sendable <= ?', timestamp())
            ->filter() as $sms)
        {
            $message = new EnvayaSMS_OutgoingMessage();
            $message->id = $sms->id;
            $message->priority = ($sms->message_type == OutgoingSMS::Transactional) ? 1 : 0;
            $message->message = $sms->message;
            $message->to = $sms->to_number;
            $messages[] = $message;
        }
        
        return $messages;
    }
    
    function render_response($replies, $controller)
    {
        $messages = array();
        
        foreach ($replies as $reply)
        {        
            $message = new EnvayaSMS_OutgoingMessage();
            $message->message = $reply;
            $message->priority = 1;
            $messages[] = $message;
        }
        
        foreach ($this->get_outgoing_messages() as $message)
        {
            $messages[] = $message;
            $log_suffix .= " +out({$message->id})";            
        }
        
        $controller->set_content_type('text/xml');
        $controller->set_content(EnvayaSMS::get_request()->get_action()->get_response_xml($messages));
    }
    
    function get_log_line()
    {
        $req = EnvayaSMS::get_request();
    
        return parent::get_log_line() . " v{$req->version} {$req->get_action()->message_type}$log_suffix";        
    }    
    
    function can_send_sms()
    {
        return false;
    }    
}