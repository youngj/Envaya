<?php

class SMS_Service_News extends SMS_Service
{
    function get_id() 
    { 
        return 'core.sms.news';
    }
    
    function get_controller($request)
    {
        return new SMS_Controller_News($request);
    }
        
}