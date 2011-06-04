<?php
    /*
     * Base class for a state machine that performs some kind of static analysis
     * of a stream of PHP tokens in one or more PHP files.
     *
     * Subclasses should define their own states and implement get_next_state($token, $type).
     */

    define('S_INIT', 0);    // initial state
    
    abstract class StateMachine
    {
        protected $cur_state;
        protected $cur_path;
    
        function set_path($path)
        {
            $this->cur_path = $path;
            $this->cur_state = S_INIT;
        }
        
        protected function error($token, $type, $state_name)
        {
            $type_name = $type != null ? (' ('.token_name($type).')') : '';
            echo get_class($this)." Error: invalid token $token$type_name\n";
            echo "  file:  {$this->cur_path}\n";
            echo "  state: {$state_name}\n";
            $this->cur_state = S_INIT;        
            return S_INIT;
        }
    
        function handle_token($token, $type)
        {
            $this->cur_state = $this->get_next_state($token, $type);
        }
        
        abstract function get_next_state($token, $type);
    }       
