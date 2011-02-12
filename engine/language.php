<?php

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
        $path = Config::get('path')."languages/{$this->code}/{$this->code}_{$group_name}.php";            
        if (file_exists($path))
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