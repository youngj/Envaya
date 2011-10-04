<?php

class SMS_Request
{
    protected $to_number;
    protected $from_number;
    protected $message;
    protected $state;
    protected $service;

    function __construct($service, $provider)
    {    
        $this->to_number = $provider->get_request_to();
        
        $from_number = $provider->get_request_from();  
        $from_number = PhoneNumber::canonicalize($from_number, 
            PhoneNumber::get_country_code($this->to_number)) ?: $from_number;
                
        $this->from_number = $from_number;
        
        $this->service = $service;
                        
        $service_id = $this->service->get_id();        
        $state = $service->get_state($from_number);
            
        if ($state->id)
        {
            $lang = $state->get('lang');
            if ($lang)
            {
                Language::set_current_code($lang);
            }                        
        }
        
        // do this before render_message_html so MMS uploads are associated with the correct user
        Session::set_instance(new Session_SMS($state));
        
        $this->state = $state;
        $this->message = $provider->render_message_html();
    }           

    function get_state()
    {
        return $this->state;
    }    
    
    function get_message()
    {
        return $this->message;
    }
    
    function get_to_number()
    {
        return $this->to_number;
    }
    
    function get_from_number()
    {
        return $this->from_number;
    }    
}