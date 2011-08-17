<?php

class SMS
{
    static function send_now($from, $to, $msg)
    {
        $provider = static::get_provider();
        return $provider->send_sms($from, $to, $msg);        
    }
    
    static function send($from, $to, $msg)
    {
        FunctionQueue::queue_call(
            array('SMS', 'send_now'), array($from, $to, $msg),
            FunctionQueue::LowPriority
        );
    }            
    
    static function get_provider()
    {
        $provider_class = Config::get('sms_backend');
        return new $provider_class();
    }    
}