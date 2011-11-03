<?php

define('S_MAYBECLASSREF_CLASS', 1);

class StateMachine_MaybeClassRef extends StateMachine
{
    public $class_refs = array(/* class => list of files */);    
    function get_next_state($token, $type, $line)
    {
        switch ($this->cur_state)
        {
            case S_INIT:                        
                if ($type == T_CONSTANT_ENCAPSED_STRING)
                {            
                    if (preg_match('#^[\'"][A-Z]\w+[\'"]$#', $token))
                    {
                        $class = eval("return $token;");
                        $this->class_refs[$class][]  = "{$this->cur_path}:$line";
                    }
                }
                else if ($type == T_STRING)
                {
                    if (preg_match('#^[A-Z]\w+$#', $token))
                    {
                        $this->class_refs[$token][]  = "{$this->cur_path}:$line";
                    }        
                }
                else if ($type == T_CLASS || $type == T_INTERFACE)
                {
                    return S_MAYBECLASSREF_CLASS;
                }
                return S_INIT;
            case S_MAYBECLASSREF_CLASS: // after 'class' or 'interface', expect class name
                if ($type == T_STRING)
                {
                    return S_INIT;
                }
                $this->error($token, $type, $line);
        }    
    }           
}