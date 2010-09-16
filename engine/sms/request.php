<?php

class SMS_Request
{
    protected $phone_number;
    protected $message;
    protected $org;
    protected $state;
    protected $replies; 

    function __construct($phone_number, $message)
    {
        $this->phone_number = $phone_number;
        $this->message = $message;        
        $org_phone_number = OrgPhoneNumber::query()->where('phone_number = ?', $phone_number)->get();
        if ($org_phone_number)
        {
            $this->org = $org_phone_number->get_org();
        }   
        
        $state = SMS_State::query()->where('phone_number = ?', $phone_number)->get();
        if (!$state)
        {
            $state = new SMS_State();
            $state->phone_number = $phone_number;
        }
        $this->state = $state;
        $this->replies = array();
    }
       
    function execute()
    {
        $sms_action = SMS_Action::parse($this->message);    
        $sms_action->execute($this);
        $this->state->save();
        
        header('Content-Type: text/xml');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<Response>';
        foreach ($this->replies as $reply)
        {
            echo "<Sms>".escape($reply)."</Sms>";
        }
        echo '</Response>';
        
    }

    function get_state($arg)
    {
        return $this->state->get_arg($arg);
    }

    function reset_state()
    {
        return $this->state->reset();
    }    
    
    function set_state($arg, $value)
    {
        return $this->state->set_arg($arg, $value);
    }
    
    function reply($msg)
    {
        $this->replies[] = $msg;
        error_log("SMS reply: $msg");
    }
    
    function get_org()
    {
        return $this->org;
    }
}