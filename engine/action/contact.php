<?php

class Action_Contact extends Action
{     
    protected function get_redirect_url()
    {
        return "/";
    }
    
    protected function get_recipient_email()
    {
        return Config::get('admin_email');
    }
    
    protected function get_email_subject()
    {
        return "User feedback";
    }

    protected $message;
    protected $name;
    protected $email;
    
    protected function get_email_body()    
    {
        return "From: {$this->name}\n\nEmail: {$this->email}\n\n{$this->message}";
    }
    
    function process_input()
    {
        $this->message = get_input('message');
        $this->name = get_input('name');
        $this->email = get_input('email');
        
        if (!$this->message)
        {
            throw new ValidationException(__('message:empty'));
        }
        
        if (!$this->email)
        {
            throw new ValidationException(__('message:email_empty'));
        }

        EmailAddress::validate($this->email);
            
        $mail = OutgoingMail::create($this->get_email_subject(), $this->get_email_body());
        $mail->setReplyTo($this->email);
        $mail->addTo($this->get_recipient_email());    
        $mail->send();
        
        SessionMessages::add(__('message:feedback_sent'));
        $this->redirect($this->get_redirect_url());
    }    
}    