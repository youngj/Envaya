<?php

require_once __DIR__.'/phpanalyzer.php';
require_once __DIR__.'/statemachine.php';

define('S_CLASS', 1);
define('S_EXTENDS', 2);

class PHPParentAnalyzer extends PHPAnalyzer
{    
    public $parent_classes = array();    
    public $paths = array();
        
    function is_checked_file($path)
    {
        return preg_match('#engine/.*\.php$#', $path);
    }

    function is_checked_dir($path)
    {
        return preg_match('#\b(engine|mod)#', $path);
    }    
    
    function parse_file_contents($contents)
    {   
        $tokens = token_get_all($contents);     
    
        $class_name = null;
       
        $state = S_INIT;
        
        foreach ($tokens as $token)
        {
            if (isset($token[1])) 
            {
                $type = $token[0];
                
                if ($type == T_WHITESPACE)
                {
                    continue;
                }
                else if ($type == T_CLASS)
                {
                    $state = S_CLASS;
                }
                else if ($type == T_STRING && $state == S_CLASS)
                {
                    $class_name = $token[1];
                    $this->paths[$class_name] = $this->rel_path;                    
                }
                else if ($type == T_EXTENDS)
                {
                    $state = S_EXTENDS;
                }
                else if ($type == T_STRING && $state == S_EXTENDS)
                {
                    $parent_name = $token[1];
                    $this->parent_classes[$class_name] = $parent_name;
                    $state = S_INIT;
                }
            }
            else if ($token == '{')
            {
                break;
            }
        }                       
    }
}


