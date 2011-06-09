<?php
    /*
     * Base class for a state machine that performs some kind of static analysis
     * of a stream of PHP tokens in one or more PHP files.
     *
     * Subclasses should define their own states and implement get_next_state($token, $type).
     */

    define('S_INIT', 0);    // initial state
    define('S_IGNORE_FILE', -1); // ignoring current file
    
    abstract class StateMachine
    {
        public $cur_state;
        protected $cur_path;
    
        public $ignore_regexes = array();
    
        function set_path($path)
        {
            $this->cur_path = $path;
            $this->cur_state = S_INIT;            
        }
        
        /*
         * Returns the offset in lines for the character at offset $offset within $token.
         * (e.g. line offset 0 means $offset is within the initial line of $token)
         */
        protected static function get_line_offset($token, $offset)
        {            
            $pre_lines = explode("\n", substr($token, 0, $offset));
            return sizeof($pre_lines) - 1;
        }
       
        
        protected function error($token, $type, $line)
        {
            $type_name = $type != null ? (' ('.token_name($type).')') : '';
            echo get_class($this)." Error: unexpected token $token$type_name\n";
            echo "  cur_path:  {$this->cur_path}:$line\n";
            echo "  cur_state: {$this->cur_state}\n";
            $this->cur_state = S_INIT;        
            return S_INIT;
        }
        
        abstract function get_next_state($token, $type, $line);
    }       
