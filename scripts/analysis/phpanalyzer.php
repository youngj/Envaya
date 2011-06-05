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
        protected $state_machines = array();
        protected $base_dir = null;
        
        public function add_state_machine($state_machine)
        {
            $this->state_machines[] = $state_machine;
        }
        
        public function parse_dir($dir)
        {
            $this->base_dir = $dir;
            $this->parse_dir_rec($dir);
            $this->base_dir = null;
        }
        
        private function parse_dir_rec($dir)
        {
            $handle = opendir($dir);
            while ($file = readdir($handle))
            {
                $path = "$dir/$file";
            
                if (preg_match('/\.php$/', $file))
                {
                    $this->parse_file($path);
                }              
                if ($file[0] != '.' && $file != 'vendors' && $file != 'languages' && $file != 'test' 
                    && is_dir($path))
                {
                    $this->parse_dir_rec($path);
                }
            }
            closedir($handle);            
        }        
        
        public function parse_file($path)
        {                   
            if ($this->base_dir)
            {
                $rel_path = substr($path, strlen($this->base_dir) + 1);
            }
            else
            {
                $rel_path = basename($path);
            }
        
            $state_machines = array();
            foreach ($this->state_machines as $state_machine)
            {
                $state_machine->set_path($rel_path);
                $state_machines[] = $state_machine;
            }
            
            $php = file_get_contents($path);
            $tokens = token_get_all($php);            
            
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
    