<?php

class SMSSubscription extends Entity
{
    static $table_name = 'sms_subscriptions';
    static $table_attributes = array(
        'description' => '',
        'language' => '',
        'phone_number' => '',
        'local_id' => 0,
    );
    
    function notify($msg)
    {
        $service = new SMS_Service_News();
    
        $msg .= "\nTo stop these notifications, txt \"STOP {$this->local_id}\"";
        
        $state = $service->get_state($this->phone_number);        
        $state->set('default_stop', $this->guid);
        $state->save();
           
        $sms = $service->create_outgoing_sms($this->phone_number, $msg);
        //$sms->message_type = OutgoingSMS::Transactional;
        $sms->send();
    }       
}