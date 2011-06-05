<?php
    /*
     * State machine that collects information about usage of language keys by observing calls to
     * the "__()" function.
     */

    define('S_LANG_UNDERSCORE', 1);
    define('S_LANG_CALL', 2);
    define('S_LANG_KEY', 3);
    
    class StateMachine_LanguageKeys extends StateMachine
    {
        public $lang_keys = array(/* key => list of files */);    
        
        private $cur_key = '';
    
        function get_next_state($token, $type, $line)
        {
            switch ($this->cur_state)
            {
                case S_INIT:    // look for calls to functions that take language keys as 1st parameter
                    if ($type == T_STRING && ($token == '__' || $token == 'add_js_string'))
                    {
                        return S_LANG_UNDERSCORE;
                    }
                    return S_INIT;
                case S_LANG_UNDERSCORE: // previous token was a function name, expect (
                    if ($token == '(')
                    {
                        return S_LANG_CALL;
                    }
                    return $this->error($token, $type, $line);
                case S_LANG_CALL:    // in call to '__(' function, look for a hardcoded language key                    
                    if ($type == T_CONSTANT_ENCAPSED_STRING)
                    {
                        $this->cur_key = eval("return $token;");
                        return S_LANG_KEY;
                    }
                    return S_INIT;
                case S_LANG_KEY: // ignore keys followed by a concatenation operator
                    if ($token == '.')
                    {
                        return S_INIT;
                    }
                    else
                    {
                        $this->lang_keys[$this->cur_key][] = "{$this->cur_path}:$line";
                        return S_INIT;
                    }
            }
        }
    }
    