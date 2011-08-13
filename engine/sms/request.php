<?php

class SMS_Request
{
    protected $to_number;
    protected $from_number;
    protected $message;
    protected $user;
    protected $state;
    protected $service;

    function __construct($from_number, $to_number, $message)
    {
        $from_number = PhoneNumber::canonicalize($from_number);
        $to_number = PhoneNumber::canonicalize($to_number);
        
        if (!$from_number || !$to_number)
        {
            throw new Exception("Invalid SMS phone number");
        }
        
        $this->service = SMS_Service::route($to_number);    
        $this->from_number = $from_number;
        $this->to_number = $to_number;
        $this->message = $message;        
        
        $user_phone_number = UserPhoneNumber::query()->where('phone_number = ?', $from_number)->get();
        if ($user_phone_number)
        {
            $this->user = $user_phone_number->get_user();
        }   
        
        $service_id = $this->service->get_id();        
        $state = SMS_State::query()
            ->where('service_id = ?', $service_id)
            ->where('phone_number = ?', $from_number)
            ->get();
            
        if (!$state)
        {
            $state = new SMS_State();
            $state->service_id = $service_id;
            $state->phone_number = $from_number;
        }
        $this->state = $state;
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
    
    function get_user()
    {
        return $this->user;
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