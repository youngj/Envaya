<?php
    /*
     * Class for performing static analysis of Envaya's PHP code. 
     * 
     * Uses PHP's tokenizer library, passing each parsed PHP token to one or more StateMachine
     * instances that implement particular analysis/data collection rules.
     */

    require_once __DIR__.'/analyzer.php';

    class PHPAnalyzer extends Analyzer
    {            
        function is_checked_file($path)
        {
            return preg_match('/\.php$/', $path);
        }
        function is_checked_dir($path)
        {
            return !preg_match('#\b(vendors|languages|test)$#', $path);
        }
        
        function parse_file_contents($contents)
        {   
            $tokens = token_get_all($contents);            
            $state_machines = $this->get_active_state_machines();
            
            foreach ($tokens as $token)
            {
                if (isset($token[1])) // performance hack to avoid function call overhead of is_array
                {
                    $type = $token[0];
                    
                    if ($type == T_WHITESPACE)
                        continue;

                    $line = $token[2];                        
                    $token = $token[1];                    
                }
                else
                {
                    $type = null;
                    // $line stays the same as previous iteration
                }
                                
                foreach ($state_machines as $state_machine)
                {                
                    $state_machine->cur_state = $state_machine->get_next_state($token, $type, $line);   
                }
            }                       
        }
    }
    