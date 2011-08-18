<?php

class SMS extends Model
{
    static $table_name = 'outgoing_sms';

    static $table_attributes = array(
        'from_number' => '',
        'to_number' => '',
        'to_name' => '',        
        'message' => '',
        'time_created' => 0,
        'time_sent' => 0,        
    );   
    
    static function create($from, $to, $msg)
    {
        $sms = new SMS();
        $sms->time_created = time();
        $sms->from_number = PhoneNumber::canonicalize($from);
        if (!$sms->from_number)
        {
            throw new InvalidParameterException("Invalid SMS phone number $from");
        }
        $sms->to_number = PhoneNumber::canonicalize($to);       
        if (!$sms->to_number)
        {
            throw new InvalidParameterException("Invalid SMS phone number $to");
        }        
        $sms->message = $msg;
        return $sms;
    }
    
    function send_now()
    {
        $provider = static::get_provider();
        $provider->send_sms($this->from_number, $this->to_number, $this->message);        
        $this->time_sent = time();
        $this->save();
    }
    
    function send($immediate = false)
    {
        if ($immediate)
        {
            $this->save();            
            $this->send_now();
        }
        else
        {
            $this->enqueue();            
        }        
    }            
    
    function enqueue()
    {
        $this->save();
        
        return FunctionQueue::queue_call(array('SMS', 'send_now_by_id'), 
            array($this->id),
            FunctionQueue::LowPriority
        );    
    }    
    
    static function send_now_by_id($id)
    {
        $sms = SMS::query()->where('id = ?', $id)->get();
        if (!$sms)
        {
            throw new InvalidParameterException("SMS id $id does not exist");
        }
        
        if ($sms->time_sent)
        {  
            throw new InvalidParameterException("SMS id $id has already been sent");
        }            

        $sms->send_now();
    }
    
    static function get_provider()
    {
        $provider_class = Config::get('sms_backend');
        return new $provider_class();
    }    
}