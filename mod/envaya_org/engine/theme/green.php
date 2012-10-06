<?php

class Theme_Green extends Theme_Solid
{
    static $thumbnail = '/_media/images/green.png';

    static function get_display_name()
    {
        return __('design:theme:green');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#035415',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#035415',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#f0fff0',
            'selected_menu_color' => '#333',
            'menu_color' => '#fff',
            'content_border' => '#333',
            'content_bg' => '#f0fff0',
            'footer_color' => '#fff',  
            'footer_link_color' => '#39f', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
