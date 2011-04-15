<?php

class Theme
{
    private $layout = 'layouts/default';
    private $name;
    private $hidden = false;
    private $css;

    private static $all_themes = array(
        'green'         => array(),
        'brick'         => array(),
        'craft4'        => array(),
        'craft1'        => array(),
        'cotton2'       => array(),
        'wovengrass'    => array(),
        'beads'         => array(),
        'red'           => array(),        
        'sidebar'       => array('layout' => 'layouts/two_column_left_sidebar'),        
        'simple'        => array('hidden' => true),
        'simple_wide'   => array('hidden' => true, 'css' => 'simple', 'layout' => 'layouts/one_column_wide'),
        'editor'        => array('hidden' => true),
        'editor_wide'   => array('hidden' => true),
        'home'          => array('hidden' => true),
    );            
    
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
    
    function get_layout()
    {
        return $this->layout;
    }    
    
    static function get($name)
    {
        if (isset(static::$all_themes[$name]))
        {
            return new Theme($name, static::$all_themes[$name]);
        }
        else
        {
            throw new Exception("Theme $name not found");
        }
    }
    
    static function all_names()
    {
        return array_keys(static::$all_themes);
    }
    
    static function available_names()
    {
        $names = array();
        foreach (static::$all_themes as $name => $value)
        {
            if (!@$value['hidden'])
            {
                $names[] = $name;
            }
        }
        return $names;
    }
}