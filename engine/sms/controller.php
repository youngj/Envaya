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

    function reply($msg)
    {
        $this->replies[] = substr($msg, 0, 160);
    }
}