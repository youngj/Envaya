<?php

class SMS_Service_Contact extends SMS_Service
{
    function get_id() 
    { 
        return 'core.sms.contact';
    }
    
    function get_controller($request)
    {
        return new SMS_Controller_Contact($request);
    }
}