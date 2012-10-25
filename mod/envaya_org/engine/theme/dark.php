<?php

class Theme_Dark extends Theme_Solid
{
    static $thumbnail = '/_media/images/dark.png';

    static function get_display_name()
    {
        return __('design:theme:dark');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#333',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#666',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#666',
            'selected_menu_color' => '#fff',
            'menu_color' => '#fff',
            'content_color' => '#ccc',
            'content_border' => '#999',
            'content_bg' => '#222',
            'footer_color' => '#fff',  
            'footer_link_color' => '#39f', 
            'snippet_color' => '#999',
            'date_color' => '#666',
        ));
    }
}
