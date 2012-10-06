<?php

class Theme_Blue extends Theme_Solid
{
    static $thumbnail = '/_media/images/blue.png';

    static function get_display_name()
    {
        return __('design:theme:blue');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#240c8d',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#240c8d',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#eef0ff',
            'selected_menu_color' => '#333',
            'menu_color' => '#fff',
            'content_border' => '#fff',
            'content_link_color' => '#1f66a5', 
            'content_bg' => '#eef0ff',
            'footer_color' => '#fff',  
            'footer_link_color' => '#ccf', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
