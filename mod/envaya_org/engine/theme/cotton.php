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
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => 'background:cotton',
            'header_bg' => '#e3d2a7',
            'header_color' => '#715023',
            'tagline_color' => '#a07d28',
            'content_bg' => 'background:beige_gradient',
            'selected_menu_bg' => '#e3d2a7',
            'selected_menu_color' => '#000',
            'menu_color' => '#fff',
            'content_color' => '#333',
            'subheader_color' => '#fff',
            'footer_color' => '#fff',
            'subheader_bg' => 'section_header:beige',
            'border_bg' => '#715023',
            'footer_bg' => '#715023',
            'translate_bg' => '#c6b186',
            'translate_color' => '#000',
            'translate_border' => '#e3d2a7',
        ));
    }
}
