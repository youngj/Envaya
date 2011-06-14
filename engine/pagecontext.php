<?php
/*
 * A place for temporarily storing various pieces of data/state that are later used
 * when rendering the page (kind of like global variables). 
 * 
 * Used by views to modify state outside of their scope (e.g. views that are rendered in the <body> tag
 * can add JS/CSS/etc in the <head> tag). Generally views are not allowed to modify state,
 * except via the PageContext class.
 */
class PageContext
{
    private static $translations_available = array();
    private static $header_html = array();
    private static $submenus = array();    
    private static $js_strings = array();
    private static $dirty = false;
    private static $http_headers = array();
    
    static function set_http_header($name, $value)
    {
        static::$http_headers[$name] = $value;
    }
        
    static function get_http_headers()
    {
        return static::$http_headers;
    }
    
    /*
     * Makes a particular localized string from the PHP languages/ files 
     * available in Javascript via __[$key] (in the user's current language).
     */
    static function add_js_string($key)
    {
        static::$js_strings[$key] = true;
    }        
    
    /*
     * Adds a snippet of HTML to the <head> tag.
     */
    static function add_header_html($html)
    {
        static::$header_html[] = $html;
    }    
    
    /*
     * Notes that a particular piece of translatable content appears on the page, 
     * which the user may wish to translate into their own language.
     */    
    static function add_available_translation($translation)
    {
        static::$translations_available[] = $translation;
    }

    /*
     * Marks the page as dirty, so that the user will be shown a JS onbeforeunload dialog
     * when leaving the page unless the dirty state is cleared (e.g. through submitting a form).
     */
    static function set_dirty($dirty = true)
    {
        static::$dirty = $dirty;
    }
    
    
    
    static function has_translation($mode=TranslateMode::All)
    {
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
        
    static function is_dirty()
    {
        return static::$dirty;
    }    
    
    static function get_header_html()
    {
        return implode('', static::$header_html);
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
        $res = array();
        foreach (array_keys(static::$js_strings) as $key)
        {
            $res[$key] = __($key);
        }
        return $res;
    }
    
    static function translate_listener($event, $object_type, $translation)
    {
        static::add_available_translation($translation);
    }

}