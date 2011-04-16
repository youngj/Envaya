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
            redirect_back_error(__('feedback:empty'));
        }
        
        if (!$this->email)
        {
            redirect_back_error(__('feedback:email_empty'));
        }

        try
        {
            validate_email_address($this->email);
        }
        catch (ValidationException $ex)
        {
            redirect_back_error($ex->getMessage());
        }            
            
        $mail = Zend::mail($this->get_email_subject(), $this->get_email_body());
        $mail->setReplyTo($this->email);
        $mail->addTo($this->get_recipient_email());    
        
        send_mail($mail);
        
        SessionMessages::add(__('feedback:sent'));
        forward($this->get_redirect_url());
    }    
}    