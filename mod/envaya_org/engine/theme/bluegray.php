<?php

class Theme_BlueGray extends Theme_Solid
{
    static $thumbnail = '/_media/images/bluegray.png';

    static function get_display_name()
    {
        return __('design:theme:bluegray');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#b1bec8',
            'header_color' => '#111',
            'tagline_color' => '#111',
            'subheader_bg' => '#b1bec8',
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
