<?php

class SMSSubscription extends Entity
{
    static $table_name = 'sms_subscriptions';
    static $table_attributes = array(
        'description' => '',    // the corresponding SMS command, e.g. 'N jeanmedia'; displayed by 'SS'
        'language' => '',
        'phone_number' => '',
        'local_id' => 0,        // each phone_number has its own local_id namespace
        'last_notification_time' => 0,
        'num_notifications' => 0,
    );
    
    function notify($msg)
    {
        $time = timestamp();
        
        // arbitrary notification rate limit: 1 notification in 20 seconds (for this notification)
        if ($time - $this->last_notification_time < 20) 
        {    
            return;
        }

        // arbitrary notification rate limit: 5 notifications in 5 minutes (total across all notifications)
        if (OutgoingSMS::query()
            ->where('message_type = ?', OutgoingSMS::Notification)
            ->where('to_number = ?', $this->phone_number)
            ->where('time_created > ?', $time - 300)
            ->count() >= 5)
        {
            return;
        }
        
        // arbitrary notification rate limit: 24 notifications in 24 hours (total across all notifications)
        if (OutgoingSMS::query()
            ->where('message_type = ?', OutgoingSMS::Notification)
            ->where('to_number = ?', $this->phone_number)
            ->where('time_created > ?', $time - 86400)
            ->count() >= 24)
        {            
            return;
        }
        
        $service = new SMS_Service_News();
    
        $msg .= "\n".sprintf(__('sms:notification_stop', $this->language), $this->local_id);
        
        $state = $service->get_state($this->phone_number);        
        $state->set('default_stop', $this->guid);
        $state->save();
           
        $sms = $service->create_outgoing_sms($this->phone_number, $msg);
        //$sms->message_type = OutgoingSMS::Transactional;
        $sms->send();
        
        $this->last_notification_time = $time;
        $this->num_notifications += 1;
        $this->save();
    }       
}