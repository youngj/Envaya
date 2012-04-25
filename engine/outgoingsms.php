<?php

class OutgoingSMS extends Model
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
        'notifier_guid' => null, // optional guid of entity that sent this message, e.g. Comment, DiscussionMessage, SMSTemplate
        'subscription_guid' => null, // guid of SMSSubscription, if applicable    
        'from_number' => '',
        'to_number' => '',
        'to_name' => '',        
        'message' => '',
        'message_type' => 0,
        'status' => 0,
        'time_sendable' => 0,        
        'time_created' => 0,
        'time_sent' => 0,   
        'error_message' => '',
    );   
    
    function get_approximate_timezone_id()
    {
        $country_code = PhoneNumber::get_country_code($this->to_number);
        return Geography::get_default_timezone_id($country_code);
    }
    
    function calculate_sendable_time()
    {
        $now = timestamp();
    
        if ($this->message_type == OutgoingSMS::Transactional)
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
            
            $min_day_minute = 9 * 60; // earliest sending time: 9 AM
            $max_day_minute = 21 * 60; // latest sending time: 9 PM
            
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
    
    function save()
    {
        if (!$this->time_created)
        {
            $this->time_created = timestamp();
        }
        parent::save();
    }
    
    function get_provider()
    {   
        $from_number = $this->from_number;
    
        foreach (Config::get('sms:routes') as $route)
        {
            if ($route['self_number'] === $from_number)
            {
                $cls = $route['provider'];
                return new $cls();
            }
        }
        
        throw new InvalidParameterException("No SMS provider defined for from_number $from_number");
    }
    
    function send_now()
    {
        $provider = $this->get_provider();
    
        if ($provider->can_send_sms())
        {
            // todo catch sending errors
            $provider->send_sms($this);        
            $this->time_sent = timestamp();
            $this->status = OutgoingSMS::Sent;
        }
        else
        {
            $this->status = OutgoingSMS::Queued;           
        }
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
                $this->status = OutgoingSMS::Waiting;
                $this->save();
            }
        }        
    }            
    
    function enqueue()
    {               
        $this->status = OutgoingSMS::Queued;
        $this->save();
        
        $provider = $this->get_provider();
        
        if ($provider->can_send_sms())
        {
            TaskQueue::queue_task(array('OutgoingSMS', 'send_now_by_id'), 
                array($this->id),
                TaskQueue::LowPriority
            );    
        }
    }    
    
    static function send_now_by_id($id)
    {
        $sms = OutgoingSMS::query()->where('id = ?', $id)->get();
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
    
    function get_message_type_text()
    {
        switch ($this->message_type)
        {
            case static::Notification: return __('sms:notification');
            case static::Transactional: return __('sms:transactional');
        }
        return '';
    }

    function get_status_text()
    {
        switch ($this->status)
        {            
            case static::Failed:    return __('email:failed');
            case static::Queued:    return __('email:queued');                
            case static::Sent:      return __('email:sent');
            case static::Waiting:   return sprintf(__('sms:waiting'), friendly_time($this->time_sendable, array('showTime' => true)));
        }
        return '';        
    }    
}