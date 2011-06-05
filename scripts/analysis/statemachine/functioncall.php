<?php

/*
 * State machine that collects information about PHP function calls from a stream of parser tokens.
 */

define('S_FCALL_NAME', 1);
define('S_FCALL_FUNCTION', 2);

class StateMachine_FunctionCall extends StateMachine
{
    public $function_calls = array(/* function_name => called_paths */);
    
    private $cur_function_name = '';
              
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:        // look for something that is possibly a function name        
                if ($type == T_STRING)
                {
                    $this->cur_function_name = $token;
                    return S_FCALL_NAME;
                }
                if ($type == T_FUNCTION) // detour state for 'function' keyword (to ignore definitions)
                {
                    return S_FCALL_FUNCTION;
                }
                return S_INIT;
            case S_FCALL_FUNCTION: // function is currently being defined, not called
                if ($token == '&')
                {
                    return S_FCALL_FUNCTION;
                }
                if ($token == T_STRING || $token == '(')
                {
                    return S_INIT;
                }
            case S_FCALL_NAME: // already seen a possible function name, look for '(' 
                if ($token == '(')
                {   
                    $this->function_calls[$this->cur_function_name][] = "{$this->cur_path}:$line";
                    return S_INIT;
                }
                return S_INIT;
        }
    }
}
