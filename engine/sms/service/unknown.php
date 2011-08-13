<?php

class SMS_Service_Unknown extends SMS_Service
{
    function get_id() 
    { 
        return 'core.sms.unknown';
    }
    
    function get_default_controller()
    {
        return new SMS_Controller_Unknown();
    }
}