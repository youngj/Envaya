<?php

/*
 * Represents an email message that the website wants to send.
 *
 * These get stored in the database so we can see what emails are being sent out,
 * and also so administrators can resend them in case of an error.
 *
 * This wraps a Zend_Mail object.
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
        'notifier_guid' => 0, // optional guid of entity that sent this message, e.g. Comment, DiscussionMessage, EmailTemplate
        'subscription_guid' => 0, // guid of EmailSubscription, if applicable
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
    
    static function create($subject = null, $bodyText = null)
    {
        $mail = new OutgoingMail();        
        $mail->time_created = timestamp();
        //$mail->setMessageId(microtime(true)."@test.envaya.org");
        
        if ($subject)
        {
            $mail->set_subject($subject);
        }
        if ($bodyText)
        {
            $mail->set_body_text($bodyText);
        }                   
        return $mail;
    }

    function add_to($email, $name = '')
    {
        return $this->get_mail()->addTo($email, $name);
    }
    
    function add_bcc($email)
    {
        return $this->get_mail()->addBcc($email);
    }
    
    function get_from()
    {
        return $this->get_mail()->getFrom();
    }
    
    function set_from($email, $name = null)
    {
        return $this->get_mail()->setFrom($email, $name);
    }
    
    function set_from_name($name)
    {
        return $this->set_from(Config::get('mail:email_from'), $name);
    }
    
    function set_from_user($user)
    {
        $this->from_guid = $user->guid;
        $this->set_from_name($user->name);
        
        if ($user->email)
        {
            $this->set_reply_to($user->email, $user->name);
        }
    }

    function set_reply_to($email, $name = null)
    {
        return $this->get_mail()->setReplyTo($email, $name);
    }
    
    function get_subject($subject)
    {
        return $this->get_mail()->getSubject();
    }       
    
    function set_subject($subject)
    {
        return $this->get_mail()->setSubject($subject);
    }   
    
    function set_body_text($body_text)
    {
        return $this->get_mail()->setBodyText($body_text);
    }
    
    function get_body_text()
    {
        return $this->get_mail()->getBodyText();
    }
    
    function set_body_html($body_html)
    {
        $this->get_mail()->setBodyHtml($body_html);
        if (!$this->get_body_text())
        {    
            require_once Engine::$root.'/vendors/markdownify/markdownify.php';
            $md = new Markdownify(true, false, false);    
            $md->escapeInText['search'] = $md->escapeInText['replace'] = array();
            $this->set_body_text($md->parseString($body_html));
        }        
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
        $this->add_to(Config::get('mail:admin_email'));    
        return $this->send();
    }    
        
    function send_to_user($user)
    {
        if ($user->email)
        {
            $this->to_guid = $user->guid;        
            $this->add_to($user->email, $user->name);
            $this->send();
            return true;
        }
        return false;
    }    
    
    function send($immediate = false)
    {        
        if (!$this->get_from())
        {
            $this->set_from_name(Config::get('site_name'));
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
        $this->time_queued = timestamp();
        $this->save();
        
        return TaskQueue::queue_task(array('OutgoingMail', 'send_now_by_id'), 
            array($this->id),
            TaskQueue::LowPriority
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
            $this->time_sent = timestamp();
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