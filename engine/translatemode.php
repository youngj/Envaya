<?php

class TranslateMode
{
    const Disabled = 0;     // do not show translations of user content, nor allow user to view translations
    const None = 1;         // do not show translations of user content, but allow user to view translations
    const Approved = 2;     // show only human-approved translations
    const Automatic = 3;    // show machine translations        
    const All = 4;          // show all translations
    
    private static $current_mode = null;
    
    static function get_current()
    {
        return static::$current_mode ?: static::Disabled;
    }    
    
    static function set_current($mode)
    {
        static::$current_mode = $mode;
    }
}
