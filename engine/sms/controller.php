<?php

class SMS_Controller extends Router implements Serializable
{
    protected $request;
    protected $replies = array();

    function __construct($params = null)
    {
        if ($params)
        {
            $this->params = $params;
        }
    }
    
    public function serialize()
    {
        return serialize($this->params);
    }
    
    public function unserialize($data)
    {
        $this->params = unserialize($data);
    }    
    
    protected function execute_route($route, $params)
    {        
        foreach ($params as $k => $v)
        {        
            $this->params[$k] = $v;
        }
        
        $before = @$route['before'];
        if ($before)
        {
            $this->$before();
        }
        $this->before();                               
                
        $cls = @$params['controller'];
        if ($cls)
        {
            $controller = new $cls($params);
            $controller->set_request($this->request);
            $controller->set_next_controller($controller);
            $controller->before();
            $controller->action_index();
            $controller->after();
            $res = $controller;
        }
        else
        {
            $action = $params['action'];
            $this->$action();
            $res = $this;
        }
        $this->after();    
        
        return $res;
    }    
    
    function get_state($name)
    {
        return $this->request->get_state($name);
    }

    function set_state($name, $value)
    {
        $this->request->set_state($name, $value);
    }
    
    function set_request($request)
    {
        $this->request = $request;
    }

    function set_next_controller($controller)
    {
        $this->request->set_initial_controller($controller);
    }
    
    function get_replies()
    {
        return $this->replies;
    }

    function reply($reply)
    {
        $reply = substr($reply, 0, 160);
    
        // prevent auto-reply loop
        $message = $this->request->get_message();
        
        $last_message = $this->get_state('_last_message');
        $last_reply = $this->get_state('_last_reply');
        $last_time = (int)$this->get_state('_last_time');            
        $num_same_message = (int)$this->get_state('_num_same_message');    
        $num_same_reply = (int)$this->get_state('_num_same_reply');  
        
        $time = timestamp();
                
        if ($time - $last_time > 3600) // reset counters after 1 hour of inactivity
        {        
            $num_same_message = 0;
            $num_same_reply = 0;
        }

        if ($last_reply == $reply)
        {
            $num_same_reply++;
        }
        else
        {
            $num_same_reply = 0;
        }
            
        if ($last_message == $message)
        {
            $num_same_message++;
        }
        else
        {
            $num_same_message = 0;
        }
        
        $this->set_state('_last_time', $time);
        $this->set_state('_last_reply', $reply);
        $this->set_state('_last_message', $message);
        $this->set_state('_num_same_reply', $num_same_reply);
        $this->set_state('_num_same_message', $num_same_message);        
        
        // accept at most 2 consecutive SMS with same message, or
        // send at most 4 consecutive SMS with same reply before dropping reply
        if ($num_same_message >= 2 || $num_same_reply >= 4)
        {
            return;
        }
                
        $this->replies[] = $reply;
    }
}