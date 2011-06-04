<?php

/*
 * State machine that collects strings that are possibly the names of callback functions
 * (with many false positives)
 */

define('S_FCALLBACK_MAYBECALL', 1);
define('S_FCALLBACK_INCALL', 2);

class StateMachine_FunctionCallback extends StateMachine
{
    public $potential_callbacks = array(/* possible_function_name => paths */);
    
    private $cur_paren_nesting = 0;
    
    function set_path($path)
    {
        parent::set_path($path);
        $this->cur_paren_nesting = 0;
    }    
    
    function get_next_state($token, $type)
    {
        if ($token == '(')
        {
            $this->cur_paren_nesting++;
        }
        else if ($token == ')')
        {
            $this->cur_paren_nesting--;
            
            if ($this->cur_paren_nesting <= 0) // definitely not in a function call anymore
            {
                $this->cur_paren_nesting = 0;
                return S_INIT;
            }            
        }
        
        switch ($this->cur_state)
        {
            case S_INIT:
                if ($type == T_STRING) // possibly a function name
                {
                    return S_FCALLBACK_MAYBECALL;
                }
                return S_INIT;
            case S_FCALLBACK_MAYBECALL: // already seen a possible function name, look for '('
                if ($token == '(')
                {
                    return S_FCALLBACK_INCALL;
                }
                return S_INIT;
            case S_FCALLBACK_INCALL:    // already seen something like "fn(", look for constant string parameters
                if ($type == T_CONSTANT_ENCAPSED_STRING) 
                {
                    $str = eval("return $token;");
                    
                    // ignore strings that could not possibly be function names
                    if (!preg_match('/[^\w]/', $str)) 
                    {
                        $this->potential_callbacks[$str][] = $this->cur_path;
                    }
                }
                return S_FCALLBACK_INCALL;
        }
        
    }
}
