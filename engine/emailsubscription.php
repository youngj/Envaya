<?php

abstract class EmailSubscription extends Subscription
{    
    static $table_base_class = 'EmailSubscription';
    static $table_name = 'email_subscriptions';
    static $table_attributes = array(    
        'subtype_id' => '',
        'language' => '',
        'email' => '',
        'name' => '',
        'last_notification_time' => 0,
        'num_notifications' => 0,
    );    
    
    // subscription that each user's email is automatically subscribed to for their own account
    static $self_subscription_classes = array(
        'EmailSubscription_Comments',
    );
    
    static function init_self_subscription($user)
    {
        $email = $user->email;
        if ($email)
        {
            static::init_for_entity($user, $email, array(
                'owner_guid' => $user->guid, 
                'language' => $user->language
            ));
        }
    }
        
    function get_key()
    {
        return $this->email;
    }
    
    static function delete_for_entity($entity, $email)
    {
        foreach (static::query_for_entity($entity)
            ->show_disabled(true)
            ->where('email = ?', $email)
            ->filter() as $subscription)
        {
            $subscription->delete();
        }
    }
    
    static function init_for_entity($entity, $email, $defaults = null)
    {
        if (!EmailAddress::is_valid($email))
        {
            return null;
        }
    
        $subscription = static::query_for_entity($entity)
            ->show_disabled(true)
            ->where('email = ?', $email)
            ->get();
            
        $cls = get_called_class();

        if (!$subscription)
        {
            $subscription = new $cls();
            $subscription->set_container_entity($entity);
            $subscription->email = $email;            
            $subscription->set_owner_entity(Session::get_logged_in_user());
            $subscription->language = Language::get_current_code();   

            if ($defaults)
            {
                foreach ($defaults as $prop => $val)
                {
                    $subscription->$prop = $val;
                }
            }                        
            $subscription->save();
        }
        
        return $subscription;
    }        
           
    function get_settings_url()
    {
        return static::get_all_settings_url($this->email) . "&id={$this->guid}";
    }
    
    static function get_all_settings_url($email)
    {
        $code = static::get_email_fingerprint($email);
        return "/pg/email_settings?e=".urlencode($email)."&c={$code}";
    }
    
    static function get_email_fingerprint($email)
    {
        return substr(md5($email . Config::get('site_secret') . "-email"), 0,15);
    }
    
    function get_description()
    {
        return 'unknown subscription';
    }
    
    function get_recipient_description()
    {
        return "\"{$this->get_name()}\" <$this->email>";
    }
    
    function get_name()
    {
        $owner = $this->get_owner_entity();
        return $owner ? $owner->name : ($this->name ?: __('email:somebody', $this->language));
    }   
    
    function render_html_body($body_content)
    {
        $footer = view('emails/notification_footer', array(
            'subscription' => $this, 
        ));
    
        return view('emails/html_layout', array(
            'body' => $body_content . $footer            
        ));        
    }
    
    function send($args)
    {
        $body = ''; // required, must be html
                
        $notifier = null;
        $from_name = null;
        $subject = null;
        $mail = null;        
        $reply_to = null;
        extract($args);
    
        if (!$mail)
        {
            $mail = OutgoingMail::create();
        }
        
        if ($subject) 
        {
            $mail->set_subject($subject);
        }
        
        if ($from_name)
        {
            $mail->set_from_name($from_name);
        }
        
        if ($notifier)
        {
            $mail->notifier_guid = $notifier->guid;
        }
        
        if ($reply_to)
        {
            $mail->set_reply_to($reply_to);
        }        
    
        $mail->set_body_html($this->render_html_body($body));
    
        $mail->add_to($this->email, $this->get_name());
        $mail->to_guid = $this->owner_guid;        
        
        $mail->subscription_guid = $this->guid;     
        $mail->send();
        
        $time = timestamp();
        
        $this->last_notification_time = $time;
        $this->num_notifications += 1;
        $this->save();        
    }       
}
