<?php

class Theme
{
    private $layout = 'layouts/default';
    private $viewtype = 'default';
    private $name;
    private $hidden = false;
    private $css;
    
    private static $loaded_themes = array();
    private static $loaded_all = false;
    
    public function __construct($name, $options)
    {
        $this->css = $this->name = $name;
        
        foreach ($options as $k => $v)
        {
            $this->$k = $v;
        }
    }
    
    function get_css_name()
    {
        return $this->css;
    }
    
    function get_viewtype()
    {
        return $this->viewtype;
    }
    
    function get_layout()
    {
        return $this->layout;
    }    
    
    static function get($name)
    {
        return static::load($name);
    }
    
    static function load($name)
    {    
        $theme = @static::$loaded_themes[$name];        
        if ($theme === null)
        {
            if (preg_match('/[^\w]/',$name))
            {
                throw new InvalidParameterException("Invalid theme name $name");
            }
            
            $path = get_real_path("themes/{$name}.php");                    
            
            if (!$path)
            {
                $theme = new Theme(Config::get('fallback_theme'), array());
            }
            else
            {        
                $options = include($path);
                $theme = new Theme($name, $options);
            }
            static::$loaded_themes[$name] = $theme;
        }
        
        return $theme;
    }
    
    private static function load_all_in_dir($dir_name)
    {
        if ($handle = opendir("{$dir_name}themes"))
        {
            while ($file = readdir($handle))
            {
                if (preg_match('/^(\w+).php$/', $file, $matches))
                {
                    static::load($matches[1]);
                }
            }
        }            
    }
    
    static function load_all()
    {
        if (!static::$loaded_all)
        {
            static::load_all_in_dir(Config::get('path'));
            foreach (Config::get('modules') as $module_name)
            {
                static::load_all_in_dir(get_module_path($module_name));
            }        
            static::$loaded_all = true;
        }
    }
    
    static function all_names()
    {
        static::load_all();
        return array_keys(static::$loaded_themes);
    }
    
    static function available_names()
    {
        static::load_all();
        $names = array();
        foreach (static::$loaded_themes as $name => $theme)
        {
            if (!$theme->hidden)
            {
                $names[] = $name;
            }
        }
        return $names;
    }
}
