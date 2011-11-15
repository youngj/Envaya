<?php
/*
 * A place for temporarily storing various pieces of data/state that are later used
 * when rendering the page (kind of like global variables). 
 * 
 * Used by views to modify state outside of their scope (e.g. views that are rendered in the <body> tag
 * can add JS/CSS/etc in the <head> tag). Generally views are not allowed to modify state,
 * except via the PageContext class.
 */
class PageContext extends Mixable
{
    private static $header_html = array();
    private static $submenus = array();    
    private static $js_strings = array();
    private static $inline_js = array();
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
    
    static function add_inline_js_file($js_path)
    {
        static::$inline_js[] = get_inline_js($js_path);
    }
    
    static function add_inline_js($js)
    {
        static::$inline_js[] = $js;
    }    
    
    /*
     * Marks the page as dirty, so that the user will be shown a JS onbeforeunload dialog
     * when leaving the page unless the dirty state is cleared (e.g. through submitting a form).
     */
    static function set_dirty($dirty = true)
    {
        static::$dirty = $dirty;
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
        if (!isset(static::$submenus[$group])) 
        {
            $submenu = new Submenu();
            static::$submenus[$group] = $submenu;
			return $submenu;
        }		
        return static::$submenus[$group];            
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
    
    static function get_inline_js()
    {
        return implode("\n", static::$inline_js);
    }
}