<?php

/*
 * State machine that collects strings that are possibly the names of callback functions
 * (with many false positives)
 */

define('S_FCALLBACK_KEYLOOKUP', 1);
define('S_FCALLBACK_STRING', 2);
 
class StateMachine_FunctionCallback extends StateMachine
{
    public $potential_callbacks = array(/* possible_function_name => path */);        
    
    private $cur_string = '';
    
    function get_next_state($token, $type, $line)
    {        
        switch ($this->cur_state)
        {
            case S_INIT:
                if ($type == T_CONSTANT_ENCAPSED_STRING) 
                {
                    $str = eval("return $token;");
                    
                    // ignore strings that could not possibly be function names
                    if (preg_match('/^[a-zA-Z_]\w+$/', $str)) 
                    {
                        $this->cur_string = $str;
                        return S_FCALLBACK_STRING;
                    }
                }
                if ($token == '[')
                    return S_FCALLBACK_KEYLOOKUP;

                return S_INIT; 
            case S_FCALLBACK_KEYLOOKUP: // ignore keys in arr['foo'] syntax
                return S_INIT;
            case S_FCALLBACK_STRING:
                if ($token != '=>') // ignore keys in array('foo' => ...) syntax
                {
                    $this->potential_callbacks[$this->cur_string] = "{$this->cur_path}:$line";                                   
                }
                return S_INIT;
        }
    }
}
