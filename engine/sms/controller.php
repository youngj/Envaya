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

    function set_default_action($value)
    {
        $this->set_state('_default', $value);
    }
    
    function get_default_action()
    {
        return $this->get_state('_default');
    }
    
    function login($user)
    {
        $this->set_state('user_guid', $user->guid);
        Session::set_loggedin_user($user);
    }
    
    function logout()
    {
        $this->set_state('user_guid', null);
        Session::set_loggedin_user(null);
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
        $reply = trim($reply);
    
        // prevent auto-reply loop
        $last_reply = $this->get_state('_last_reply');
        $last_time = (int)$this->get_state('_last_time');            
        $num_same_reply = (int)$this->get_state('_num_same_reply');  
        
        $time = timestamp();
                
        if ($time - $last_time > 3600) // reset counters after 1 hour of inactivity
        {        
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
            
        $this->set_state('_last_time', $time);
        $this->set_state('_last_reply', $reply);
        $this->set_state('_num_same_reply', $num_same_reply);
        
        // accept at most 6 consecutive SMS with same reply before dropping reply
        if ($num_same_reply >= 6)
        {
            return;
        }
                
        $this->replies[] = $reply;
    }
}