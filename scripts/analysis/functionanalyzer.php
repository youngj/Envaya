<?php

require_once __DIR__."/phpanalyzer.php";
require_once __DIR__."/statemachine/functiondef.php";
require_once __DIR__."/statemachine/functioncall.php";
require_once __DIR__."/statemachine/functioncallback.php";

class FunctionAnalyzer extends PHPAnalyzer
{   
    function print_results()
    {
        foreach ($this->print_functions as $print_function)
        {
            $print_function();
        }
    }
    
    protected $print_functions = array();
    protected $machines_by_class = array();
    protected function use_state_machine($cls)
    {
        if (!isset($this->classes[$cls]))
        {
            $sm = new $cls();
            $this->machines_by_class[$cls] = $sm;
            $this->add_state_machine($sm);
        }
        return $this->machines_by_class[$cls];
    }

    protected function get_state_machine($cls)
    {
        return $this->machines_by_class[$cls];
    }

    function add_mode_all()
    {
        $this->add_mode_camel();
        $this->add_mode_unused();
        $this->add_mode_args();
    }
    
    function add_mode_camel()
    {    
        $function_sm = $this->use_state_machine('StateMachine_FunctionDef');
    
        $this->print_functions[] = function() use ($function_sm) {        
            print "Camel case functions:\n";
            foreach ($function_sm->function_info as $fullName => $info)
            {
                if (preg_match('/[A-Z]/', $info['name'])
                && !preg_match('/^__/', $info['name'])
                && !preg_match('#\btest/#', $info['path']))
                {
                    echo "$fullName\n";
                    echo "  {$info['path']}\n";
                }
            }
            print "\n";
        };
    }

    function add_mode_unused()
    {    
        $function_sm = $this->use_state_machine('StateMachine_FunctionDef');
        $fcall_sm = $this->use_state_machine('StateMachine_FunctionCall');
        $fcallback_sm = $this->use_state_machine('StateMachine_FunctionCallback');
        
        $this->print_functions[] = function() use ($function_sm, $fcall_sm, $fcallback_sm) {        
            print "Unused functions:\n";
            
            $function_calls = $fcall_sm->function_calls;
            $potential_callbacks = $fcallback_sm->potential_callbacks;
            
            foreach ($function_sm->function_info as $fullName => $info)
            {
                $functionName = $info['name'];
                if (!preg_match('/^(__|action_)/', $functionName)     
                    && !preg_match('#\btest/#', $info['path']) 
                    && !isset($function_calls[$functionName])
                    && !isset($potential_callbacks[$functionName]))
                {
                    echo "$fullName\n";
                    echo "  {$info['path']}\n";
                }
            }
            print "\n";
        };
    }

    function add_mode_args()
    {    
        $function_sm = $this->use_state_machine('StateMachine_FunctionDef');
        
        $this->print_functions[] = function() use ($function_sm) {        
            print "Functions with many arguments:\n";
            foreach ($function_sm->function_info as $functionName => $info)
            {
                $numArgs = sizeof($info['args']);
                if ($numArgs > 3)
                {
                    echo "$numArgs $functionName\n";
                    echo "  {$info['path']}\n";
                }
            }
            print "\n";
        };
    }
}
