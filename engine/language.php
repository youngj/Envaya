<?php

/*
 * Interface for retrieving localized strings for the UI.
 * Typically used via __('key') .
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
 * opaque keys (e.g. _('This is a translatable string.') instead of __('translatable_str')
 * because a change to the original text requires a change to all language files,
 * and also because the same English text may be translated multiple ways in another language.
 */
class Language
{
    static $languages = array();
    
    static function get($code)
    {
        return @static::$languages[$code];
    }
    
    static function current()
    {
        return static::get(get_language());
    }
    
    static function get_options()
    {        
        $options = array();
        foreach (static::$languages as $k => $v)
        {
            $options[$k] = __($k, $k);
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
  
    function __construct($code)
    {
        $this->code = $code;
        $this->translations = array();
        $this->loaded_files = array();
    }

    function add_translations($language_array)
    {
        if ($language_array)
        {
            foreach ($language_array as $k => $v)
            {
                $this->translations[$k] = $v;
            }    
        }
    }
    
    function get_translation($key)
    {
        $res = @$this->translations[$key];        
        if ($res !== null)
        {
            return $res;
        }
                
        if ($this->load('default'))
        {
            $res = @$this->translations[$key];        
            if ($res !== null)
            {
                return $res;
            }
        }
        $keyArr = explode(':', $key, 2);
        if (sizeof($keyArr) == 2 && $this->load($keyArr[0]))
        {
            $res = @$this->translations[$key];        
            if ($res !== null)
            {
                return $res;
            }        
        }
        
        if ($this->load('admin'))
        {
            $res = @$this->translations[$key];        
            if ($res !== null)
            {
                return $res;
            }
        }        
                
        return null;
    }   
    
    function load_all()
    {
        if ($handle = opendir(Config::get('path')."languages/{$this->code}"))
        {
            while ($file = readdir($handle))
            {
                if (preg_match('/^'.$this->code.'_(\w+).php$/', $file, $matches))
                {
                    $this->load($matches[1]);
                }
            }
        }
    }
    
    function load($group_name)
    {        
        if (!@$this->loaded_files[$group_name])
        {            
            $this->add_translations($this->get_group($group_name));            
            $this->loaded_files[$group_name] = true;
            return true;
        }
        return false;
    }    
    
    function get_group($group_name)
    {
        $path = get_real_path("languages/{$this->code}/{$this->code}_{$group_name}.php");    
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
}