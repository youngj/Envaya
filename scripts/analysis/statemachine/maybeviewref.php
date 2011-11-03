<?php

class StateMachine_MaybeViewRef extends StateMachine
{
    public $views = array(/* key => list of files */);    
    function get_next_state($token, $type, $line)
    {
        if ($type == T_CONSTANT_ENCAPSED_STRING && preg_match('#^[\'"][a-z][a-z\_/]+[a-z][\'"]$#', $token))
        {
            $view = eval("return $token;");
            $this->views[$view][]  = "{$this->cur_path}:$line";
        }
    }           
}