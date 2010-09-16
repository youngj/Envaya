<?php

class SMS_Action_Invalid extends SMS_Action
{
    protected $message;

    function __construct($message)
    {
        $this->message = $message;
    }

    function execute($sms_request)
    {
        $sms_request->reply("Could not understand the command: {$this->message}");
    }    
}