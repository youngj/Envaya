<?php

abstract class Theme
{
    static $layout = 'layouts/default';
    static $viewtype = 'default';    
    static $css;
        
    static function get_display_name()
    {
        return get_called_class();
    }
    
    static function get_css_name()
    {
        return static::$css;
    }
    
    static function get_viewtype()
    {
        return static::$viewtype;
    }
    
    static function get_layout()
    {
        return static::$layout;
    }    
    
    static function get_subtype_id()
    {
        return ClassRegistry::get_subtype_id(get_called_class());
    }
    
    static function render_custom_css($theme_options)
    {    
        return '';
    }
    
    static function get_class($theme_id)
    {
        $theme = ClassRegistry::get_class($theme_id);
        if (!$theme || !is_subclass_of($theme, 'Theme'))
        {
            return Config::get('theme:default');
        }
        else
        {
            return $theme;
        }
    }            
}
