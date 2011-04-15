<?php

/*
 * A place for temporarily storing various pieces of data/state that are later used
 * when rendering the page (kind of like global variables). 
 * 
 * Used in cases when passing the data as variables to the relevant views is too 
 * tedious.
 */
class PageContext
{
    private static $translatable = true;
    private static $translations_available = array();
    private static $rss = false;
    private static $header_html = array();
    private static $submenus = array();    
    private static $js_strings = array();
    private static $dirty = false;
    
    static function set_translatable($translatable)
    {
        static::$translatable = $translatable;
    }
        
    static function is_translatable($mode=TranslateMode::All)
    {
        if (!static::$translatable)
        {
            return false;
        }

        foreach (static::$translations_available as $translation)
        {
            if ($mode == TranslateMode::All || $mode == TranslateMode::ManualOnly && $translation->owner_guid)
            {
                return true;
            }
        }
        return false;
    }
    
    static function has_translation_error()
    {
        foreach (static::$translations_available as $translation)
        {
            if (!$translation->id)
            {
                return true;
            }
        }    
        return false;
    }

    static function has_stale_translation()
    {
        foreach (static::$translations_available as $translation)
        {
            if ($translation->is_stale())
            {
                return true;
            }
        }
        return false;
    }
    
    static function get_original_language()
    {
        if (!empty(static::$translations_available))
        {
            return static::$translations_available[0]->get_original_language();
        }
        return Language::get_current_code();
    }
    
    static function get_available_translations()
    {
        return static::$translations_available;
    }
    
    static function add_available_translation($translation)
    {
        static::$translations_available[] = $translation;
    }

    static function has_rss()
    {
        return static::$rss;
    }    
    
    static function set_rss($rss)
    {
        static::$rss = $rss;
    }
   
    static function set_dirty($dirty = true)
    {
        static::$dirty = $dirty;
    }
    
    static function is_dirty()
    {
        return static::$dirty;
    }
    
    static function add_header_html($key, $html)
    {
        if (!isset(static::$header_html[$key]))
        {
            static::$header_html[$key] = $html;
        }
    }
    
    static function get_header_html()
    {
        $res = '';
        foreach (static::$header_html as $key => $html)
        {
            $res .= $html;
        }
        return $res;
    }   
       
    static function get_submenu($group = 'topnav') 
    {
        $submenu = @static::$submenus[$group];
        if (!$submenu) 
        {
            $submenu = new Submenu();
            static::$submenus[$group] = $submenu;
        }
        return $submenu;            
    }
        
    static function get_js_strings()
    {
        return array_keys(static::$js_strings);
    }

    static function add_js_string($key)
    {
        static::$js_strings[$key] = true;
    }    
}