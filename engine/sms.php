<?php

class SMS extends Model
{
    // message_type constants
    const Notification = 0;
    const Transactional = 1;
    
    // status constants
    const Queued = 1; 
    const Failed = 2;
    const Sent = 3;
    const Waiting = 4; // SMS held temporarily until time_sendable (next daytime hour)
    
    static $table_name = 'outgoing_sms';

    static $table_attributes = array(
        'from_number' => '',
        'to_number' => '',
        'to_name' => '',        
        'message' => '',
        'message_type' => 0,
        'status' => 0,
        'time_sendable' => 0,        
        'time_created' => 0,
        'time_sent' => 0,        
    );   
    
    function get_approximate_timezone_id()
    {
        $country_code = PhoneNumber::get_country_code($this->to_number);
        return Geography::get_default_timezone_id($country_code);
    }
    
    function calculate_sendable_time()
    {
        $now = timestamp();
    
        if ($this->message_type == SMS::Transactional)
        {
            return $now;
        }
        else // avoid sending SMS notifications at nighttime
        {
            $local_time = new DateTime("@{$now}");                            
            $timezone_id = $this->get_approximate_timezone_id();            
            
            if ($timezone_id)
            {            
                $local_time->setTimeZone(new DateTimeZone($timezone_id));
            }
            
            // number of minutes after midnight in recipient's local time in range [0,1440)
            $local_day_minute = (60 * (int)$local_time->format('G')) + (int)$local_time->format('i');
            
            $min_day_minute = 10 * 60; // earliest sending time: 10 AM
            $max_day_minute = 20 * 60; // latest sending time: 8 PM
            
            if ($local_day_minute >= $min_day_minute && $local_day_minute <= $max_day_minute)
            {
                return $now;
            }
            else
            {
                $minutes_in_day = 24 * 60;
                
                // number of minutes from now until next allowed sending time
                $diff_day_minutes = ($minutes_in_day + $min_day_minute - $local_day_minute) % $minutes_in_day;

                return $now + $diff_day_minutes * 60;
            }
        }
    }
    
    static function create($from, $to, $msg)
    {
        $sms = new SMS();
        $sms->time_created = timestamp();
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
        // todo catch sending errors
        $provider = static::get_provider();        
        $provider->send_sms($this->from_number, $this->to_number, $this->message);        
        $this->time_sent = timestamp();
        $this->status = SMS::Sent;
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
            $this->time_sendable = $this->calculate_sendable_time();
        
            if ($this->time_sendable <= timestamp())
            {                        
                $this->enqueue();            
            }
            else
            {
                $this->status = SMS::Waiting;
                $this->save();
            }            
        }        
    }            
    
    function enqueue()
    {               
        $this->status = SMS::Queued;
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