<?php

class Theme_Red extends Theme_Solid
{
    static $thumbnail = '/_media/images/red.png';

    static function get_display_name()
    {
        return __('design:theme:red');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#a40004',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#a40004',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#ffdcdc',
            'selected_menu_color' => '#333',
            'menu_color' => '#fff',
            'content_border' => '#fff',
            'content_link_color' => '#1f66a5', 
            'content_bg' => '#ffdcdc',
            'footer_color' => '#fff',  
            'footer_link_color' => '#ccf', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
