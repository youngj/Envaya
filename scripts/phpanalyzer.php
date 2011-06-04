<?php
    /*
     * Generic class for performing static analysis of PHP code. 
     * 
     * Uses PHP's tokenizer library, passing each parsed PHP token to one or more StateMachine
     * instances that implement particular analysis/data collection rules.
     */

    require __DIR__.'/statemachine.php';

    class PHPAnalyzer
    {            
        private $state_machines = array();
        
        public function add_state_machine($state_machine)
        {
            $this->state_machines[] = $state_machine;
        }
        
        public function parse_dir($dir)
        {
            $handle = opendir($dir);
            while ($file = readdir($handle))
            {
                $path = "$dir/$file";
            
                if (preg_match('/\.php$/', $file))
                {
                    $this->parse_file($path);
                }              
                if ($file[0] != '.' && $file != 'vendors' && is_dir($path))
                {
                    $this->parse_dir($path);
                }
            }
            closedir($handle);            
        }
        
        public function parse_file($path)
        {           
            foreach ($this->state_machines as $state_machine)
            {
                $state_machine->set_path($path);
            }            
            
            $php = file_get_contents($path);
            $tokens = token_get_all($php);
            
            foreach ($tokens as $token)
            {
                if (is_array($token))
                {
                    $type = $token[0];
                    $token = $token[1];
                }
                else
                {
                    $type = null;
                }
                                
                foreach ($this->state_machines as $state_machine)
                {
                    $state_machine->handle_token($token, $type);
                }
            }            
        }
    }
    