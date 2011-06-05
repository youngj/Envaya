<?php

/*
 * State machine that collects information about references to PHP classes from a stream of parser tokens.
 */

define('S_CLASSREF_NEW', 1);
define('S_CLASSREF_NAME', 2);

class StateMachine_ClassRef extends StateMachine
{
    public $class_refs = array(/* class_name => referencing_paths */);
    
    private $cur_class_name = '';
              
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:        
                if ($type == T_NEW)
                {
                    return S_CLASSREF_NEW;
                }
                if ($type == T_STRING)
                {
                    $this->cur_class_name = $token;
                    return S_CLASSREF_NAME;
                }
                return S_INIT;
            case S_CLASSREF_NEW: // after 'new' operator, expect class name
                if ($type == T_STRING)
                {
                    $this->class_refs[$token][] = "{$this->cur_path}:$line";
                    return S_INIT;
                }
                if ($type == T_VARIABLE)
                {
                    return S_INIT;
                }
                $this->error($token, $type, $line);
            case S_CLASSREF_NAME: // already seen a possible class name, look for '::' 
                if ($type == T_DOUBLE_COLON)
                {   
                    $this->class_refs[$this->cur_class_name][] = "{$this->cur_path}:$line";
                    return S_INIT;
                }
                return S_INIT;
        }
    }
}
