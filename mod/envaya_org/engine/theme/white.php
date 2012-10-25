<?php

class Theme_White extends Theme_Solid
{
    static $thumbnail = '/_media/images/white.png';

    static function get_display_name()
    {
        return __('design:theme:white');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#fefefe',
            'header_color' => '#111',
            'tagline_color' => '#111',
            'subheader_bg' => '#fff',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#eee',
            'selected_menu_color' => '#111',
            'menu_color' => '#111',
            'content_border' => '#ccc',
            'content_bg' => '#fcfcfc',
            'footer_color' => '#111',  
            //'footer_link_color' => '#069', 
            'snippet_color' => '#333',            
            'date_color' => '#666',
            'box_shadow' => 'box_shadow:gray',
        ));
    }
}
