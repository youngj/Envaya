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
    
    function send_alert($subject, $msg)
    {       
        $gateways = Config::get('sms_gateways');
        
        $admin_email = @$gateways[$this->phone_number]['admin_email'];        
        if ($admin_email)
        {
            $mail = OutgoingMail::create("EnvayaSMS alert - {$this->phone_number} - $subject", $msg);
            $mail->addTo($admin_email);
            $mail->send();
        }
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