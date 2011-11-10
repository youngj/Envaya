<?php

abstract class Hook
{
    private static $handler_fns = array();

    static function register_handler($handler_cls)
    {
        return static::register_handler_fn(array($handler_cls, 'execute'));
    }
    
    static function register_handler_fn($handler_fn, $priority = null)
    {
        $cls = get_called_class();
        
        if (!isset(Hook::$handler_fns[$cls]))
        {
            Hook::$handler_fns[$cls] = array();
        }                
        
        $handler_fns = &Hook::$handler_fns[$cls];
        
        if (isset($priority))
        {
            $incr = ($priority > 0) ? 1 : -1;        
            while (isset($handler_fns[$priority])) 
            {
                $priority += $incr;
            }
            $handler_fns[$priority] = $handler_fn;
        }
        else
        {
            $handler_fns[] = $handler_fn;
        }
           
        
    }
    
    static function trigger($vars = null)
    {                
        $cls = get_called_class();        
        if (isset(Hook::$handler_fns[$cls]))
        {
            $handler_fns = Hook::$handler_fns[$cls];
            ksort($handler_fns);
            foreach ($handler_fns as $priority => $handler_fn)
            {
                $res = call_user_func($handler_fn, $vars);
                if (isset($res))
                {
                    $vars = $res;
                }
            }
        }
        
        return $vars;
    }
}

abstract class Hook_EndRequest extends Hook { }
abstract class Hook_RenderEntityProperty extends Hook { }
