<?php

abstract class SMS_Service
{
    static function get_phone_number($to_number)
    {
        $cls = get_called_class();
    
        foreach (Config::get('sms_routes') as $route)
        {
            if ($route['service'] == $cls && preg_match('#^'.$route['remote_numbers'].'$#', $to_number))
            {
                return $route['self_number'];
            }
        }
        
        throw new InvalidParameterException("No routes to $to_number for $cls");    
    }

    static function create_outgoing_sms($to_number, $message)
    {
        $from_number = static::get_phone_number($to_number);

        $sms = new OutgoingSMS();
        $sms->from_number = $from_number;
        $sms->to_number = $to_number;
        $sms->message = $message;
        return $sms;
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
    abstract function get_controller($request);
}