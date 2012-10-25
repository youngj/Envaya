<?php

class Theme_Moss extends Theme_Solid
{
    static $thumbnail = '/_media/images/moss.png';

    static function get_display_name()
    {
        return __('design:theme:moss');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#b5da9c',
            'header_color' => '#333',
            'tagline_color' => '#333',
            'subheader_bg' => '#b5da9c',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#fafffa',
            'selected_menu_color' => '#111',
            'menu_color' => '#111',
            'content_border' => '#fff',
            'content_bg' => '#fafffa',
            'footer_color' => '#111',  
            'footer_link_color' => '#069', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
