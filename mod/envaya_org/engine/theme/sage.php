<?php

class Theme_Sage extends Theme_Solid
{
    static $thumbnail = '/_media/images/sage.png';

    static function get_display_name()
    {
        return __('design:theme:sage');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#BCB88A',
            'header_color' => '#111',
            'tagline_color' => '#111',
            'subheader_bg' => '#BCB88A',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#fbfdeb',
            'selected_menu_color' => '#111',
            'menu_color' => '#111',
            'content_border' => '#fff',
            'content_bg' => '#fbfdeb',
            'footer_color' => '#111',  
            'footer_link_color' => '#069', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
