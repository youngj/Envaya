<?php

class Theme_Cotton extends Theme_UserSite
{
    static $css = 'topmenu2';    
    static $thumbnail = '/_media/images/cotton/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:cotton');
    }               
    
    static function get_vars()
    {
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'default' => 'background:cotton',
            ),
            'header_bg' => array(
                'default' => '#e3d2a7',
            ),
            'header_color' => array(
                'default' => '#715023',
            ),
            'tagline_color' => array(
                'default' => '#a07d28',
            ),
            'content_bg' => array(
                'default' => 'background:beige_gradient',
            ),
            'selected_menu_bg' => array(
                'default' => '#e3d2a7',
            ),
            'selected_menu_color' => array(
                'default' => '#000',
            ),  
            'menu_color' => array(
                'default' => '#fff',
            ),
            'content_color' => array(
                'default' => '#333',
            ),
            'subheader_color' => array(
                'default' => '#fff',
            ),        
            'footer_color' => array(
                'default' => '#fff',
            ),        
            'subheader_bg' => array(
                'default' => 'section_header:beige',
            ),
            'border_bg' => array(
                'default' => '#715023',
            ),
            'footer_bg' => array(
                'default' => '#715023',
            ),
            'translate_bg' => array(
                'default' => '#c6b186',
            ),
            'translate_color' => array(
                'default' => '#000',
            ),
            'translate_border' => array(
                'default' => '#e3d2a7',
            ),
        ));
    }
}
