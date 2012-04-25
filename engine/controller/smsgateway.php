<?php

class Controller_SMSGateway extends Controller
{
    static $routes = array(
        array(
            'regex' => '/(?P<action>\w+)\b',
            'defaults' => array('action' => 'index'),
        ),        
    );

    function before()
    {
        Permission_Public::require_any();
    }
    
    function action_app()
    {
        if (!Request::is_post())
        {
            Permission_SendMessage::require_for_root();
        
            $this->set_content(view('page/sms_app_simulator'));            
            return;
        }

        $provider = new SMS_Provider_App();
    
        if (!$provider->is_validated_request())
        {
            $this->log("{$provider->get_log_line()} INVALID");
        
            $this->set_status(403);
            $this->set_content("Invalid request signature");
            throw new RequestAbortedException();
        }            

        $request = EnvayaSMS::get_request();       
        
        $app_log = $request->log;
        if ($app_log)
        {
            $log_file = Config::get('dataroot'). "/sms_".preg_replace('#[^\w]#', '', $request->phone_number).".log";        
            $f = fopen($log_file, "a");                
            fwrite($f, $app_log);
            fclose($f);        
        } 
        
        $app_state = SMS_AppState::get_for_phone_number($request->phone_number);
        if (!$app_state->active)
        {
            $app_state->active = true;            
            $app_state->send_alert("Phone active", "{$request->phone_number} successfully connected to server");
        }        
        $app_state->save();
    
        $action = $request->get_action();
    
        switch ($action->type)
        {
            case EnvayaSMS::ACTION_INCOMING:
                return $this->receive_sms($provider);
            case EnvayaSMS::ACTION_OUTGOING:                
                $messages = $provider->get_outgoing_messages();
                
                $message_ids = array_map(function ($m) { return $m->id; }, $messages);
                
                $this->set_content_type('text/xml');
                $this->set_content($action->get_response_xml($messages));                   
                        
                $num_messages = sizeof($messages);
                $message_ids_str = implode(',', $message_ids);
                        
                $this->log("{$request->phone_number} out App v{$request->version} $num_messages $message_ids_str");
                        
                return;
            case EnvayaSMS::ACTION_SEND_STATUS:    

                $message = OutgoingSMS::query()->where('id = ?', $action->id)->get();
                
                if (!$message)
                {
                    $this->log("{$request->phone_number} stat App v{$request->version} {$message->id} not_found");
                
                    $this->set_status(404);
                    $this->set_content("Message does not exist");        
                    return;
                }
                                
                $message->status = $this->parse_sms_status($action->status);
                
                if ($message->status == OutgoingSMS::Sent)
                {
                    $message->time_sent = timestamp();
                }
                
                $message->error_message = $action->error;                
                $message->save();
                $this->set_content("OK");   

                if ($message->status == OutgoingSMS::Failed)
                {
                    $app_state->send_alert("Error sending SMS", "Error sending message to {$message->to_number}: {$message->error_message}");
                }
                
                $this->log("{$request->phone_number} -> {$message->to_number} stat App v{$request->version} {$action->id} {$action->status}");            
                return;

            case EnvayaSMS::ACTION_TEST:                
                $this->set_content("OK");
                $this->log("{$request->phone_number} test App v{$request->version}");                            
                return;
                
            case EnvayaSMS::ACTION_DEVICE_STATUS:
                $this->set_content("OK");
                                
                if ($action->status == EnvayaSMS::DEVICE_STATUS_BATTERY_LOW)
                {
                    $app_state->send_alert("Battery low", "Battery low for {$request->phone_number}");
                }
                else if ($action->status == EnvayaSMS::DEVICE_STATUS_BATTERY_OKAY)
                {
                    $app_state->send_alert("Battery okay", "Battery okay for {$request->phone_number}");
                }
                
                $this->log("{$request->phone_number} device App v{$request->version} {$action->status}");
                
                return;
            default:
                throw new NotFoundException();
        }
    }    
    
    private function parse_sms_status($sms_status)
    {
        switch ($sms_status)
        {
            case EnvayaSMS::STATUS_QUEUED:
                return OutgoingSMS::Queued;
            case EnvayaSMS::STATUS_SENT:
                return OutgoingSMS::Sent;
            case EnvayaSMS::STATUS_FAILED:
                return OutgoingSMS::Failed;
            default:
                return 0;
        }
    }       
    
    function log($str)
    {
        $log_file = Config::get('dataroot'). '/sms.log';
    
        $time = timestamp();
        $date = new DateTime("@$time");
        $date_str = $date->format("Y-m-d H:i:s");
    
        $f = fopen($log_file, "a");
        
        $ip = Request::get_client_ip();
        
        fwrite($f, "[$date_str] $ip $str\n");
        fclose($f);
    }
        
    private function get_incoming_sms_route($to_number)
    {
        foreach (Config::get('sms:routes') as $route)
        {
            if ($route['self_number'] === $to_number)
            {
                return $route;
            }
        }
        return null;
    }
    
    private function is_valid_provider_class($provider_class)
    {
        foreach (Config::get('sms:routes') as $route)
        {
            if ($route['provider'] === $provider_class)
            {
                return true;
            }
        }
        return false;    
    }
    
    function action_incoming()
    {
        $provider_class = "SMS_Provider_".get_input('provider');
        
        if (!$this->is_valid_provider_class($provider_class))
        {        
            $this->log("$provider_class UNKNOWN");
        
            $this->set_status(403);
            $this->set_content("Invalid SMS provider");
            throw new RequestAbortedException();
        }        
        
        $provider = new $provider_class();        
    
        if (!$provider->is_validated_request())
        {
            $this->log("{$provider->get_log_line()} INVALID");
        
            $this->set_status(403);
            $this->set_content("Invalid request signature");
            throw new RequestAbortedException();
        }  

        $this->receive_sms($provider);                  
    }
    
    private function receive_sms($provider)
    {
        $to_number = $provider->get_request_to();
        $route = $this->get_incoming_sms_route($to_number);
                
        if (!$route)
        {
            $service_class = 'SMS_Service_News';
        }        
        else
        {    
            $service_class = $route['service'];
        }
        $service = new $service_class();                
                
        $request = new SMS_Request($service, $provider);
        
        $controller = $service->get_controller($request);                
        $controller->execute_request();        
        
        $replies = $controller->get_replies();
        
        $provider->render_response($replies, $this);
        
        $num_replies = sizeof($replies);
        $length = 0;
        foreach ($replies as $reply)
        {
            $length += strlen($reply);
        }
                
        $action = str_replace('action_','',$controller->param('action'));
        $controller_cls = str_replace('SMS_Controller_','',get_class($controller));
                
        $this->log("{$provider->get_log_line()} $controller_cls/$action $num_replies $length");
        
        return $replies;
    }    
}
    
