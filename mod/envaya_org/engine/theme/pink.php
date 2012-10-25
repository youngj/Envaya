<?php

class Theme_Pink extends Theme_Solid
{
    static $thumbnail = '/_media/images/pink.png';

    static function get_display_name()
    {
        return __('design:theme:pink');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#dcbebe',
            'header_color' => '#333',
            'tagline_color' => '#333',
            'subheader_bg' => '#dcbebe',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#fffafa',
            'selected_menu_color' => '#111',
            'menu_color' => '#111',
            'content_border' => '#fff',
            'content_bg' => '#fffafa',
            'footer_color' => '#111',  
            'footer_link_color' => '#069', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }
}
