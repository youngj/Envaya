<?php

class Theme_Purple extends Theme_Solid
{
    static $thumbnail = '/_media/images/purple.png';

    static function get_display_name()
    {
        return __('design:theme:purple');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#42036f',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#42036f',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#ebd5fa',
            'selected_menu_color' => '#333',
            'menu_color' => '#fff',
            'content_border' => '#fff',
            'content_bg' => '#ebd5fa',
            'footer_color' => '#fff',  
            'footer_link_color' => '#39f', 
            'content_link_color' => '#069', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
