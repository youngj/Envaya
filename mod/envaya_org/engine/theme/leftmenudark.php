<?php

class Theme_LeftMenuDark extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenudark.png';

    static function get_display_name()
    {
        return __('design:theme:dark');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#333333',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#666',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#666',
            'selected_menu_color' => '#fff',
            'menu_color' => '#ccc',
            'menu_hover_color' => '#fff',
            'border_bg' => '#222',
            'content_color' => '#ccc',
            'content_border' => '#999',
            'footer_color' => '#fff',        
            'snippet_color' => '#999',
        ));
    }    
}
