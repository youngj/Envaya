<?php

/*
 * Represents an email message that the website wants to send.
 *
 * These get stored in the database so we can see what emails are being sent out,
 * and also so administrators can resend them in case of an error.
 *
 * This wraps a Zend_Mail object, and all Zend_Mail methods are available on the 
 * OutgoingMail object, except that send() is redefined.
 */
class OutgoingMail extends Model
{
    const Queued = 1;
    const Failed = 2;
    const Sent = 3;
    const Held = 4;
    const Rejected = 5;
    const Bounced = 6;

    static $table_name = 'outgoing_mail';
    static $table_attributes = array(
        'email_guid' => 0, // guid of EmailTemplate, if applicable
        'to_guid' => 0,  // guid of recipient user, if applicable        
        'from_guid' => 0, // guid of sending user, if applicable
        'time_created' => 0,
        'time_queued' => 0,
        'time_sent' => 0,
        'subject' => '',
        'to_address' => '',
        'time_sent' => 0,
        'status' => 0,
        'error_message' => '',
        'serialized_mail' => '', // serialized form of Zend_Mail object
    );   
    
    private $mail = null;
    
    /* nonexistent methods on OutgoingMail object are forwarded to Zend_Mail object */
    function __call($fn, $args)
    {
        return call_user_func_array(array($this->get_mail(), $fn), $args);
    }
    
    static function create($subject = null, $bodyText = null)
    {
        $mail = new OutgoingMail();        
        $mail->time_created = time();
        //$mail->setMessageId(microtime(true)."@test.envaya.org");
        
        if ($subject)
        {
            $mail->setSubject($subject);
        }
        if ($bodyText)
        {
            $mail->setBodyText($bodyText);
        }           
        
        return $mail;
    }
    
    function get_from_entity()
    {
        return User::get_by_guid($this->from_guid);
    }

    function get_to_entity()
    {
        return User::get_by_guid($this->to_guid);
    }

    
    function get_mail()
    {
        if (!$this->mail)
        {
            if ($this->serialized_mail)
            {
                Zend::load('Zend_Mail');
                $this->mail = unserialize($this->serialized_mail);
            }
            else
            {
                $this->mail = Zend::mail();
            }
        }
        return $this->mail;
    }
    
    function save()
    {        
        $mail = $this->get_mail();
        $this->serialized_mail = serialize($mail);
        $this->subject = $mail->getSubject();
        
        $recipients = $mail->getRecipients();
        if (sizeof($recipients))
        {
            $this->to_address = implode('; ', $recipients);
        }
        
        parent::save();
    }

    function send_to_admin()
    {
        $this->addTo(Config::get('admin_email'));    
        return $this->send();
    }    
        
    function send_to_user($user)
    {
        if ($user->email)
        {
            $this->to_guid = $user->guid;        
            $this->addTo($user->email, $user->name);
            $this->send();
            return true;
        }
        return false;
    }    
    
    function send($immediate = false)
    {        
        if (!$this->getFrom())
        {
            $this->setFrom(Config::get('email_from'), Config::get('site_name'));
        }    
    
        if ($immediate)
        {
            $this->save();            
            return $this->send_now();
        }
        else
        {
            $from_user = $this->get_from_entity();
            if ($from_user && !$from_user->is_approved())
            {
                $this->status = static::Held;
                $this->save();
            }
            else
            {
                $this->enqueue();
            }
        }    
    }
    
    function enqueue()
    {
        $this->status = static::Queued;
        $this->time_queued = time();
        $this->save();
        
        return FunctionQueue::queue_call(array('OutgoingMail', 'send_now_by_id'), 
            array($this->id),
            FunctionQueue::LowPriority
        );    
    }
    
    private function send_now()
    {        
        try
        {
            $mailer = Zend::mail_transport();   
            
            $mail = $this->get_mail();               
                
            $mail->send($mailer);        
            $this->status = static::Sent;
            $this->time_sent = time();
            $this->error_message = '';
            $this->save();
        }
        catch (Exception $ex)
        {
            $this->status = static::Failed;
            $this->error_message = $ex->getMessage();
            $this->save();
            
            throw $ex;
        }
        
        return true;
    }
    
    static function send_now_by_id($id)
    {
        $mail = OutgoingMail::query()->where('id = ?', $id)->get();
        if (!$mail)
        {
            throw new InvalidParameterException("Mail id $id does not exist");
        }
        
        if ($mail->status == OutgoingMail::Sent)
        {  
            throw new InvalidParameterException("Mail id $id has already been sent");
        }            

        $mail->send_now();
    }
    
    function has_error()
    {
        return $this->error_message && ($this->status == static::Failed || $this->status == static::Bounced);
    }
    
    function get_status_text()
    {
        switch ($this->status)
        {            
            case static::Failed:    return __('email:failed');
            case static::Bounced:   return __('email:bounced');
            case static::Queued:    return __('email:queued');                
            case static::Sent:      return __('email:sent');
            case static::Held:      return __('email:held');
            case static::Rejected:  return __('email:rejected');
        }
        return '';        
    }
}