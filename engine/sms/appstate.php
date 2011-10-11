<?php

class SMS_AppState extends Model
{    
    static $table_name = 'sms_app_state';
    static $table_attributes = array(        
        'phone_number' => '',
        'time_created' => 0,
        'time_updated' => 0,
        'active' => 1,
    );    
    
    static function send_alert($subject, $msg)
    {
        OutgoingMail::create("EnvayaSMS alert - $subject", $msg)
            ->send_to_admin();  
    }
    
    static function get_for_phone_number($phone_number)
    {
        $state = static::query()->where('phone_number = ?', $phone_number)->get();
        if (!$state)
        {
            $state = new SMS_AppState();
            $state->phone_number = $phone_number;
        }
        return $state;
    }
    
    function save()
    {
        if (!$this->time_created)
        {
            $this->time_created = timestamp();
        }
        $this->time_updated = timestamp();
        parent::save();
    }
}