<?php

abstract class SMS_Service
{
    static function create_outgoing_sms($to_number, $message)
    {
        $cls = get_called_class();
    
        foreach (Config::get('sms_routes') as $route)
        {
            if ($route['service'] == $cls && preg_match('#^'.$route['remote_numbers'].'$#', $to_number))
            {
                $sms = new OutgoingSMS();
                $sms->from_number = $route['self_number'];
                $sms->to_number = $to_number;
                $sms->message = $message;
                return $sms;
            }
        }
        
        throw new InvalidParameterException("No routes to $to_number for $cls");
    }    
    
    function get_state($phone_number)
    {    
        $service_id = $this->get_id();        
        $state = SMS_State::query()
            ->where('service_id = ?', $service_id)
            ->where('phone_number = ?', $phone_number)
            ->get();
        if (!$state)
        {
            $state = new SMS_State();
            $state->service_id = $service_id;
            $state->phone_number = $phone_number;
        }
        return $state;
    }
    
    abstract function get_id();
    abstract function get_default_controller();        
}