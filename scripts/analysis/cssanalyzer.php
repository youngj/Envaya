<?php
    /*
     * Class for performing static analysis of Envaya's CSS code. 
     */
    require_once __DIR__.'/analyzer.php';

    class CSSAnalyzer extends Analyzer
    {            
        function is_checked_file($path)
        {
            return preg_match('/\.css$/', $path);
        }

        function is_checked_dir($path)
        {
            return preg_match('#\b(www|_media)\b#', $path);
        }
        
        function parse_file_contents($contents)
        {   
            foreach ($this->get_active_state_machines() as $state_machine)
            {                
                // currently we don't parse CSS, 
                // just treat it as one big inline html token and use PHP state machines
                $state_machine->cur_state = $state_machine->get_next_state($contents, T_INLINE_HTML, 1);   
            }
        }
    }
    