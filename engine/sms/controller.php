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

    function _reply($reply)
    {
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
    
    function get_more()
    {
        $chunk_index = ($this->get_state('chunk_index') ?: 0) + 1;
        $chunks = $this->get_state('chunks');        
                
        if ($chunks && $chunk_index < sizeof($chunks))
        {
            return array($chunks, $chunk_index);
        }
        return null;
    }
    
    function reply_more()
    {
        $more = $this->get_more();
        if ($more)
        {
            list($chunks, $chunk_index) = $more;        
            $this->_reply($chunks[$chunk_index]);
            $this->set_state('chunk_index', $chunk_index);
        }
        else
        {
            $this->_reply(__('sms:no_more_content'));
            $this->set_state('chunks', null);
        }           
    }
    
    // when we split long messages into chunks, max_parts is the maximum number of SMS parts in one chunk
    // 1 = 160 characters
    // 2 = 153*2 characters
    // 3 = 153*3 characters
    // etc
    function set_max_parts($max_parts)
    {
        $this->set_state('max_parts', $max_parts);
    }
    
    function get_max_parts()
    {
        return $this->get_state('max_parts') ?: 2;
    }
    
    function set_chunks($chunks)
    {
        $this->set_state('chunk_index', 0);
        $this->set_state('chunks', (sizeof($chunks) > 1) ? $chunks : null);                    
    }
        
    function reply($reply)
    {        
        // split long replies into chunks, only send first one
        
        $chunks = SMS_Output::split_text($reply, $this->get_max_parts());
        $this->set_chunks($chunks);
                        
        $this->_reply($chunks[0]);
    }
}