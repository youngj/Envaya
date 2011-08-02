<?php

class TranslateMode
{
    const None = 1;
    const Approved = 2;
    const Automatic = 3;        
    const All = 4;
    
    private static $current_mode = null;
    
    static function get_current()
    {
        if (static::$current_mode == null)
        {
            static::$current_mode = ((int)get_input("trans")) ?: TranslateMode::Approved;
        }
        return static::$current_mode;
    }    
    
    static function set_current($mode)
    {
        static::$current_mode = $mode;
    }
}
