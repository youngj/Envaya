<?php

class Theme_LeftMenuBlue extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenublue.png';

    static function get_display_name()
    {
        return __('design:theme:blue');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#240c8d',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#240c8d',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#240c8d',
            'menu_hover_color' => '#1f66a5',
            'selected_menu_color' => '#fff',
            'menu_color' => '#333',
            'border_bg' => '#eef0ff',
            //'content_color' => '#ccc',
            //'content_border' => '#999',
            'footer_color' => '#fff',  
            //'footer_link_color' => '#aaf', 
            'content_link_color' => '#1f66a5', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }    
}
