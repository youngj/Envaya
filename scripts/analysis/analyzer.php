<?php
    /*
     * Generic class for performing static source code analysis on a directory tree.
     * (The analyzed source code may or may not be PHP.)
     *
     * Subclasses define parse_file_contents, which tokenizes the file
     * and delegates to each of the state machines to perform particular analysis rules.
     */

    require_once __DIR__.'/statemachine.php';

    abstract class Analyzer
    {            
        protected $state_machines = array();
        protected $base_dir = null;
        protected $rel_path = null;
        
        abstract function is_checked_file($path);
        abstract function is_checked_dir($path);
        abstract function parse_file_contents($contents);        
        
        public function add_state_machine($state_machine)
        {
            $this->state_machines[] = $state_machine;
        }
        
        public function get_active_state_machines()
        {
            $machines = array();
            foreach ($this->state_machines as $machine)
            {
                if ($machine->cur_state != S_IGNORE_FILE)
                {
                    $machines[] = $machine;
                }
            }
            return $machines;
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
            
                if (is_file($path) && $this->is_checked_file($path))
                {
                    $this->parse_file($path);
                }              
                if ($file[0] != '.' && is_dir($path) && $this->is_checked_dir($path))
                {
                    $this->parse_dir_rec($path);
                }
            }
            closedir($handle);            
        }        
        
        public function get_rel_path($path)
        {
            if ($this->base_dir)
            {
                 return substr($path, strlen($this->base_dir) + 1);
            }
            else
            {
                return basename($path);
            }        
        }
        
        public function parse_file($path)
        {                   
            $this->rel_path = $rel_path = $this->get_rel_path($path);
            
            foreach ($this->state_machines as $state_machine)
            {
                $state_machine->set_path($rel_path);
            }
            
            $contents = file_get_contents($path);            
            $this->parse_file_contents($contents);            
        }
    }
    
