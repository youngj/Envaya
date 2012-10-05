<?php

abstract class Theme_UserSite extends Theme
{
    static $thumbnail;    

    static $available_themes = array();    
    static $all_patterns = array();
        
    static function render_custom_css($theme_options)
    {    
        $types = static::get_types();
    
        $css = '';
        foreach (static::get_vars() as $name => $props)
        {                    
            if (@$props['hidden'])
            {
                continue;
            }
        
            $value = isset($theme_options[$name]) ? $theme_options[$name] : $props['default'];
        
            if (preg_match('/^#[\dabcdef]+$/i', $value))
            {
                $pattern = $value;
            }
            else
            {
                $pattern = static::get_pattern($value);                
            }
                        
            if ($pattern)
            {            
                $type = $props['type'];
                $selector = $props['selector'];
                $css_template = $types[$type];
                
                if (!$type || !$selector || !$css_template)
                {
                    throw new InvalidParameterException("Invalid theme option: $name (type = $type, selector = $selector)");
                }
                
                $css .= strtr($css_template, array('SELECTOR' => $selector, 'VALUE' => $pattern))."\n";
            }
        }
        return $css;        
    }
    
    static function get_pattern($pattern_name)
    {
        return @self::$all_patterns[$pattern_name];
    }
        
    static function get_patterns()
    {
        return self::$all_patterns;
    }    
    
    static function get_thumbnail()
    {
        return static::$thumbnail;
    }           
    
    static function add_patterns($patterns)
    {
        foreach ($patterns as $name => $css)
        {   
            self::$all_patterns[$name] = $css;
        }
    }
    
    static function merge_vars($vars, $child_vars)
    {
        foreach ($child_vars as $name => $props)
        {        
            $vars[$name] = isset($vars[$name]) ? array_merge($vars[$name], $props) : $props;        
        }       
        return $vars;
    }

    static function set_defaults($vars, $defaults)
    {
        foreach ($defaults as $var => $value)
        {
            $vars[$var]['default'] = $value;
        }
        return $vars;
    }
    
    static function get_types()
    {
        return array(
            'background' => 'SELECTOR {background:VALUE}',
            'border_color' => 'SELECTOR {border-color:VALUE}',
            'box_shadow' => 'SELECTOR { box-shadow:VALUE;-moz-box-shadow:VALUE}',
            'text_color' => 'SELECTOR {color:VALUE}',
            'selected_menu_background' => "SELECTOR.selected, SELECTOR:hover { background:VALUE }"        
        );
    }
    
    static function add_available_themes($themes)
    {
        foreach ($themes as $theme)
        {    
            self::$available_themes[] = $theme;
        }
    }
    
    static function get_available_themes()
    {   
        return self::$available_themes;
    }
        
    
    static function get_options()
    {
        return array();         
    }
    
    static function get_vars()
    {
        return array(
            'body_bg' => array(
                'type' => 'background',
                'selector' => 'body',                    
                'default' => '',            
            ),    
            'header_bg' => array(
                'type' => 'background',
                'selector' => '.heading_container',    
                'default' => '',
            ),
            'header_color' => array(       
                'type' => 'text_color',
                'selector' => '#heading h2, #heading a',
                'default' => '#000'
            ),
            'tagline_color' => array(       
                'type' => 'text_color',
                'selector' => '#heading h3',
                'default' => '#000'
            ),                
            'selected_menu_bg' => array(       
                'type' => 'selected_menu_background',        
                'selector' => '#site_menu a',        
                'default' => '',
            ),
            'selected_menu_color' => array(
                'type' => 'text_color',        
                'selector' => '#site_menu a.selected, #site_menu a:hover',        
                'default' => '#000',
            ),
            'menu_color' => array(       
                'type' => 'text_color',        
                'selector' => '#site_menu a',        
                'default' => '#686464',
            ),   
            'translate_bg' => array(
                'type' => 'background',
                'selector' => '#translate_bar',
                'default' => '#948f87',
            ),
            'translate_color' => array(
                'type' => 'text_color',
                'selector' => '#translate_bar, #translate_bar a',
                'default' => '#fff',
            ),
            'translate_border' => array(
                'type' => 'border_color',
                'selector' => '#translate_bar',
                'default' => '#fff',
            ),
            'main_bg' => array(
                'type' => 'background',
                'selector' => '.content_container',                    
                'default' => '',
            ),
            'border_bg' => array(
                'type' => 'background',
                'selector' => '.content_container .thin_column',        
                'default' => '',
            ),   
            'subheader_bg' => array(
                'type' => 'background',
                'selector' => '.section_header',        
                'default' => '',
            ),        
            'content_bg' => array(
                'type' => 'background',
                'selector' => '.section_content',        
                'default' => '',
            ),
            'content_color' => array(       
                'type' => 'text_color',        
                'selector' => '.section_content',        
                'default' => '#333',
            ), 
            'content_link_color' => array(
                'type' => 'text_color',
                'selector' => '.section_content a',        
                'default' => '#4690D6',        
            ),
            'footer_color' => array(       
                'type' => 'text_color',        
                'selector' => '.footer_container, .shareLinks',        
                'default' => '#333',
            ),       
            'footer_link_color' => array(
                'type' => 'text_color',        
                'selector' => '.footer_container a, .shareLinks a',        
                'default' => '#4690D6',        
            ),
            'subheader_color' => array(
                'type' => 'text_color',        
                'selector' => '.section_header',        
                'default' => '#000',        
            ),
            'footer_bg' => array(
                'type' => 'background',
                'selector' => '.footer_container .thin_column',        
                'default' => '',
            ), 
            'button_bg' => array(
                'type' => 'background',
                'selector' => '.submit_button div',        
                'default' => '#2e4973',
            ),
            'button_color' => array(
                'type' => 'text_color',
                'selector' => '.submit_button',        
                'default' => '#fff',
            ),
            'help_color' => array(
                'type' => 'text_color',
                'selector' => '.help',        
                'default' => '#666',
            ),
            'snippet_color' => array(
                'type' => 'text_color',
                'selector' => '.feed_snippet',        
                'default' => '#666',
            ),
            'date_color' => array(
                'type' => 'text_color',
                'selector' => '.blog_date',        
                'default' => '#aaa',
            ),            
        );
    }
}
