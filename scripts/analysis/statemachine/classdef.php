<?php

/*
 * State machine that collects information about definitions of PHP classes from a stream of parser tokens.
 */

define('S_CLASSDEF_CLASS', 1);

class StateMachine_ClassDef extends StateMachine
{
    public $class_defs = array(/* class_name => paths of definition */);
              
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:        
                if ($type == T_CLASS || $type == T_INTERFACE)
                {
                    return S_CLASSDEF_CLASS;
                }
                return S_INIT;
            case S_CLASSDEF_CLASS: // after 'class' or 'interface', expect class name
                if ($type == T_STRING)
                {
                    $this->class_defs[$token][] = "{$this->cur_path}:$line";
                    return S_INIT;
                }
                $this->error($token, $type, $line);
        }
    }
}
