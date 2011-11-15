<?php

/*
 * Interface for retrieving localized strings for the UI.
 * Typically used via __( 'key' ) .
 *
 * These strings are stored in PHP files under languages/<language_code>/, each of 
 * which returns an associative array of key => localized string.
 *
 * languages/<language_code>/<language_code>_default.php is always loaded,
 * but strings can be split into multiple files that are only loaded on demand.
 *
 * Keys beginning with "<group_name>:" will be searched for in 
 * languages/<language_code>/<language_code>_<group_name>.php if not in the default file
 * 
 * While 'gettext' or '_' is perhaps a more standard way of doing internationalization, 
 * it is annoying due to its use of binary files and compilation step. Also the gettext 
 * convention of using English text as the translation key is more fragile than using 
 * opaque keys (e.g. _('This is a translatable string.') instead of __( 'translatable_str')
 * because a change to the original text requires a change to all language files,
 * and also because the same English text may be translated multiple ways in another language.
 */
class Language
{
    static $languages = array();
    static $fallback_groups = array();
    
    private static $current_code = null;
    
    static function get($code)
    {
        return @static::$languages[$code];
    }
    
    /**
    * Gets the current language in use by the system or user.
    * @return string The language code (eg "en")
    */
    static function get_current_code()
    {
        $language = '';

        if (static::$current_code)
        {
            return static::$current_code;
        }

        $language = isset($_GET['lang']) ? $_GET['lang']
            : (isset($_COOKIE['lang']) ? $_COOKIE['lang']
            : (isset($_POST['lang']) ? $_POST['lang']
            : (static::get_accept_language()
            ?: static::get_default_language_for_country(GeoIP::get_country_code()))));
        
        if (!$language || !Language::get($language))
        {
            $language = Config::get('language');
        }

        static::$current_code = $language;
        return $language;
    }
    
    private static function get_default_language_for_country($country_code)
    {
        switch ($country_code)
        {
            case 'rw': return 'rw';
            default: return null;
        }
    }

    private static function get_accept_language()
    {
        $acceptLanguage = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
        if ($acceptLanguage)
        {
            $languages = explode(",", $acceptLanguage);
            foreach ($languages as $language)
            {
                $langQ = explode(";", $language);
                $lang = trim($langQ[0]);
                $langLocale = explode("-", $lang);
                return $langLocale[0];
            }
        }
    }
    
    static function set_current_code($code)
    {
        static::$current_code = $code;
    }
    
    static function current()
    {
        return static::get(static::get_current_code());
    }
    
    static function all()
    {
        return array_values(static::$languages);
    }
    
    static function get_options()
    {        
        $options = array();
        foreach (static::$languages as $k => $v)
        {
            $options[$k] = __("lang:$k", $k);
        }

        return $options;
    }    
    
    static function init($code)
    {
        $lang = new Language($code);
        static::$languages[$code] = $lang;
        return $lang;    
    }
    
    protected $code;
    protected $translations;
    protected $loaded_files;    
  
    protected $requested_keys;
  
    function __construct($code)
    {
        $this->code = $code;
        $this->translations = array();
        $this->loaded_files = array();
        $this->requested_keys = array();
    }   
    
    function get_code()
    {
        return $this->code;
    }

    function add_translations($language_array)
    {
        if ($language_array)
        {
            $all_translations =& $this->translations;
            foreach ($language_array as $k => $v)
            {
                $all_translations[$k] = $v;
            }    
        }
    }   
   
    function get_requested_keys()
    {
        return array_keys($this->requested_keys);
    }
   
    function get_translation($key)
    {    
        $this->requested_keys[$key] = true;
    
		if (isset($this->translations[$key]))
		{
            return $this->translations[$key];
        }
        
        foreach (static::get_group_search_order($key) as $group_name)
        {
            if ($this->load($group_name))
            {
                $res = @$this->translations[$key];        
                if ($res !== null)
                {
                    return $res;
                }
            }
        }
                  
        return null;
    }   

    private function add_group_names_in_dir(&$group_names, $dir_name)
    {
        if ($handle = @opendir("{$dir_name}/languages/{$this->code}"))
        {
            while ($file = readdir($handle))
            {
                if (preg_match('/^'.$this->code.'_(\w+).php$/', $file, $matches))
                {
                    $group_names[] = $matches[1];
                }
            }
        }    
    }
        
    function get_all_group_names()
    {
        $group_names = array();        
        $this->add_group_names_in_dir($group_names, Config::get('root'));        
        foreach (Config::get('modules') as $module_name)
        {
            $this->add_group_names_in_dir($group_names, Engine::get_module_root($module_name));
        }    
        return $group_names;
    }
    
    function load_all()
    {
        foreach ($this->get_all_group_names() as $group_name)
        {
            $this->load($group_name);
        }
    }
        
    function load($group_name)
    {        
        if (!isset($this->loaded_files[$group_name]))
        {            
            $this->add_translations($this->get_group($group_name));            
            $this->loaded_files[$group_name] = true;
            return true;
        }
        return false;
    }    
    
    function get_group_path($group_name)
    {
        return Engine::get_real_path("languages/{$this->code}/{$this->code}_{$group_name}.php");    
    }
    
    function get_group($group_name)
    {
        $path = $this->get_group_path($group_name);
        if ($path)
        {
            return include($path);
        }        
        return null;
    }
    
    function get_loaded_files()
    {
        return array_keys($this->loaded_files);
    }    
    
    function get_loaded_translations()
    {
        return $this->translations;
    }    
    
    static function get_placeholders($value)
    {
        preg_match_all('/\%s|\{\w+\}/', $value, $matches);        
        return @$matches[0] ?: array();
    }
    
    /*
     * Normally, a language key like '<group_name>:foo' will be retrieved from a file named 
     * '<lang>_<group_name>.php'. By adding a fallback group, you can split keys into different groups
     * so '<group_name>:foo' can be retrieved from a file named <lang>_<fallback_group_name>.php
     */
    static function add_fallback_group($group_name, $fallback_group_name)
    {
        static::$fallback_groups[$group_name][] = $fallback_group_name;
    }
    
    static function get_group_search_order($key)
    {
        $group_names = array('default');
        
        $key_arr = explode(':', $key, 2);
        if (sizeof($key_arr == 2))
        {
            $group_name = $key_arr[0];
            
            $group_names[] = $group_name;
            
            if (isset(static::$fallback_groups[$group_name]))
            {
                foreach (static::$fallback_groups[$group_name] as $fallback_group)
                {
                    $group_names[] = $fallback_group;
                }
            }
        }
        
        $group_names[] = 'admin';
        
        return $group_names;
    }	
}