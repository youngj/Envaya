<?php

class TranslateMode
{
    const None = 1;
    const ManualOnly = 2;
    const All = 3;
    
    private static $current_mode = null;
    
    static function get_current()
    {
        if (static::$current_mode == null)
        {
            static::$current_mode = ((int)get_input("trans")) ?: TranslateMode::ManualOnly;
        }
        return static::$current_mode;
    }    
    
    static function set_current($mode)
    {
        static::$current_mode = $mode;
    }
}
