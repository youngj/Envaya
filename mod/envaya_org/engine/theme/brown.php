<?php

class Theme_Brown extends Theme_Solid
{
    static $thumbnail = '/_media/images/brown.png';

    static function get_display_name()
    {
        return __('design:theme:brown');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#C19A6B',
            'header_color' => '#111',
            'tagline_color' => '#111',
            'subheader_bg' => '#C19A6B',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#f8eee3',
            'selected_menu_color' => '#111',
            'menu_color' => '#111',
            'content_border' => '#fff',
            'content_bg' => '#f8eee3',
            'footer_color' => '#111',  
            'footer_link_color' => '#069', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
