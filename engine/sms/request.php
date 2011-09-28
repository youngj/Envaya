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
            
        if (!$state->id)
        {
            Session::set_loggedin_user(null);
        }
        else
        {
            $lang = $state->get('lang');
            if ($lang)
            {
                Language::set_current_code($lang);
            }
            
            $user_guid = $state->get('user_guid');
            Session::set_loggedin_user(User::get_by_guid($user_guid));            
        }
        $this->state = $state;                
        $this->message = $provider->render_message_html();
    }                 
    
    function get_initial_controller()
    {
        $controller = $this->get_state('initial_controller');
        if (!$controller)
        {
            return $this->service->get_default_controller();
        }
        return $controller;
    }
    
    function set_initial_controller($controller)
    {
        $this->set_state('initial_controller', $controller);
    }
        
    function get_state($name)
    {
        return $this->state->get($name);
    }

    function reset_state()
    {
        return $this->state->reset();
    }    
    
    function save_state()
    {
        $this->state->save();
    }
    
    function set_state($name, $value)
    {
        return $this->state->set($name, $value);
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