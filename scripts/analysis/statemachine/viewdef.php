<?php
    /*
     * State machine that collects information about view definitions
     */

    class StateMachine_ViewDef extends StateMachine
    {
        public $views = array(/* view name => list of paths */);    
        
        function set_path($path)
        {
            if (preg_match('#(mod/\w+)?views/(?P<viewtype>\w+)/(?P<viewname>[^\.]*)\.php$#', $path, $match))
            {
                $viewname = $match['viewname'];
                $this->views[$viewname][] = $path;
            }
        }
        
        function get_next_state($token, $type, $line)
        {
            return S_INIT;
        }
    }
    