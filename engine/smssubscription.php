<?php

abstract class SMSSubscription extends Subscription
{    
    static $table_base_class = 'SMSSubscription';
    static $table_name = 'sms_subscriptions';
    static $table_attributes = array(    
        'subtype_id' => '',
        'language' => '',
        'phone_number' => '',
        'local_id' => 0,        // each phone_number has its own local_id namespace
        'last_notification_time' => 0,
        'num_notifications' => 0,
    );
    
    // subscriptions that each user's primary phone number is automatically subscribed to for their own account
    static $self_subscription_classes = array(
        'SMSSubscription_Comments'
    );
    
    static function init_self_subscription($user)
    {
        $primary_phone = $user->get_primary_phone_number();        
        if ($primary_phone)
        {
            static::init_for_entity($user, $primary_phone, array(
                'owner_guid' => $user->guid, 
                'language' => $user->language
            ));
        }
    }
    
    function get_key()
    {
        return $this->phone_number;
    }    
    
    static function init_for_entity($entity, $phone_number, $defaults = null)
    {
        if (!PhoneNumber::can_send_sms($phone_number))
        {
            return null;
        }
    
        $cls = get_called_class();
    
        $subscription = static::query_for_entity($entity)
            ->show_disabled(true)
            ->where('phone_number = ?', $phone_number)
            ->get();
            
        if (!$subscription)
        {
            $subscription = new $cls();
            $subscription->set_container_entity($entity);
            $subscription->phone_number = $phone_number;
            $subscription->language = Language::get_current_code();
            $subscription->set_owner_entity(Session::get_logged_in_user());
            
            if ($defaults)
            {
                foreach ($defaults as $prop => $val)
                {
                    $subscription->$prop = $val;
                }
            }            
            
            $max_row = Database::get_row("SELECT max(local_id) as max FROM sms_subscriptions where phone_number = ?", 
                array($phone_number));
                
            $max_id = $max_row ? ((int)$max_row->max) : 0;
            
            $subscription->local_id = $max_id + 1; // could have concurrency issues but not the end of the world            
            $subscription->save();
        }
        
        return $subscription;
    }    
    
    function get_description()
    {
        // subclasses should override
        return "unknown";
    }
    
    function get_name()
    {
        $owner = $this->get_owner_entity();
        return $owner ? $owner->name : "(unknown name)";
    }       
    
    function get_recipient_description()
    {
        return "\"{$this->get_name()}\" <$this->phone_number>";
    }
        
    function send($args)
    {
        $message = '';
        $notifier = null;
        $append_stop = true;
        
        extract($args);    
        
        if (!$message)
        {
            throw new InvalidParameterException("missing message parameter");
        }
    
        $time = timestamp();
        
        // arbitrary notification rate limit: 1 notification in 20 seconds (for this notification)
        if ($time - $this->last_notification_time < 20) 
        {    
            return;
        }

        // arbitrary notification rate limit: 5 notifications in 5 minutes (total across all notifications)
        if (OutgoingSMS::query()
            ->where('message_type = ?', OutgoingSMS::Notification)
            ->where('subscription_guid > 0')
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
            ->where('subscription_guid > 0')
            ->where('time_created > ?', $time - 86400)
            ->count() >= 24)
        {            
            return;
        }
        
        $service = new SMS_Service_News();
    
        if ($append_stop)
        {
            $message .= "\n".sprintf(__('sms:notification_stop', $this->language), $this->local_id);
        }
                
        $state = $service->get_state($this->phone_number);        
        $state->set('default_stop', $this->guid);
        $state->save();
           
        $sms = $service->create_outgoing_sms($this->phone_number, $message);
        //$sms->message_type = OutgoingSMS::Transactional;
        
        if ($notifier)
        {
            $sms->notifier_guid = $notifier->guid;
        }
        
        $sms->subscription_guid = $this->guid;        
        $sms->send();
        
        $this->last_notification_time = $time;
        $this->num_notifications += 1;
        $this->save();
    }       
}