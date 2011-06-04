<?php

/*
 * State machine that collects information about PHP function definitions from a stream of parser tokens.
 */

define('S_FUNCTION_DEF', 1);
define('S_FUNCTION_NAME', 2);
define('S_FUNCTION_ARGS', 3);
define('S_FUNCTION_CLASS', 4);

class StateMachine_FunctionDef extends StateMachine
{
    public $function_info = array(/* function => array('args' => args, 'path' => path, 'name' => name) */);    
    
    private $cur_class_name = '';    
    private $cur_function_name = '';
    private $cur_num_args = 0;

    function set_path($path)
    {
        parent::set_path($path);
        $this->cur_class_name = '';
    }
        
    function get_next_state($token, $type)
    {
        if ($type == T_WHITESPACE)
            return $this->cur_state;
            
        switch ($this->cur_state)
        {
            case S_INIT:
                if ($type == T_FUNCTION)
                {
                    return S_FUNCTION_DEF;
                }
                if ($type == T_CLASS || $type == T_INTERFACE)
                {
                    return S_FUNCTION_CLASS;
                }
                return S_INIT;
            case S_FUNCTION_CLASS:  // previous token was 'class' or 'interface', expect a class name
                if ($type == T_STRING)
                {
                    $this->cur_class_name = "$token::";
                    return S_INIT;
                }
                return $this->error($token, $type, 'S_FUNCTION_CLASS');
            case S_FUNCTION_DEF:  // previous token was 'function', expect a function name 
                if ($type == T_STRING)
                {
                    $this->cur_function_name = $token;
                    return S_FUNCTION_NAME;
                }
                if ($token == '(') // anonymous function
                {
                    return S_INIT;
                }
                if ($token == '&') // return by reference
                {
                    return S_FUNCTION_DEF;
                }
                return $this->error($token, $type, 'S_FUNCTION_DEF');
            case S_FUNCTION_NAME:   // previous token was a function name, expect '('
                if ($token == '(')
                {
                    $this->cur_args = array();
                    return S_FUNCTION_ARGS;
                }
                return $this->error($token, $type, 'S_FUNCTION_NAME');
            case S_FUNCTION_ARGS:   // inside a function argument list, look for T_VARIABLE parameters
                if ($type == T_VARIABLE)
                {
                    $this->cur_args[] = $token;
                    return S_FUNCTION_ARGS;
                }  
                else if ($token == ')')
                {
                    $fn = "{$this->cur_class_name}{$this->cur_function_name}";
                
                    $this->function_info[$fn] = array(
                        'args' => $this->cur_args,
                        'name' => $this->cur_function_name,
                        'path' => $this->cur_path,
                    );
                    return S_INIT;               
                }
                else if ($token == '{' || $token == '}')
                {
                    return $this->error($token, $type, 'S_FUNCTION_ARGS');
                }
                return S_FUNCTION_ARGS;
        }
    }
}
