<?php
    /*
     * State machine that collects information about usage of language keys by observing calls to
     * the "__()" function.
     */

    define('S_LANG_UNDERSCORE', 1);
    define('S_LANG_KEY', 2);
    
    class StateMachine_LanguageKeys extends StateMachine
    {
        public $lang_keys = array(/* key => list of files */);    
    
        function get_next_state($token, $type)
        {
            if ($type == T_WHITESPACE)
                return $this->cur_state;
                
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
                        return S_LANG_KEY;
                    }
                    return $this->error($token, $type, "S_LANG_UNDERSCORE");
                case S_LANG_KEY:        // in call to '__(' function, look for a hardcoded language key
                    if ($type == T_CONSTANT_ENCAPSED_STRING)
                    {
                        $lang_key = eval("return $token;");
                        $this->lang_keys[$lang_key] = $this->cur_path;
                        return S_INIT;
                    }  
                    return S_INIT;                    
            }
        }
    }
    