<?php

class Theme_Yellow extends Theme_Solid
{
    static $thumbnail = '/_media/images/yellow.png';

    static function get_display_name()
    {
        return __('design:theme:yellow');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#ffe66f',
            'header_color' => '#000',
            'tagline_color' => '#000',
            'subheader_bg' => '#ffe66f',
            'subheader_color' => '#333',
            'selected_menu_bg' => '#fff6c8',
            'selected_menu_color' => '#000',
            'menu_color' => '#333',
            'content_border' => '#fff',
            'content_bg' => '#fff6c8',
            //'footer_color' => '#fff',  
            //'footer_link_color' => '#39f', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => '',
        ));
    }
}
