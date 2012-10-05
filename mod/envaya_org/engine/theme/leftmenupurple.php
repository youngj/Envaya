<?php

class Theme_LeftMenuPurple extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenupurple.png';

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
            'selected_menu_bg' => '#42036f',
            //'selected_menu_bg' => '#ffe66f',
            'menu_hover_color' => '#1f66a5',
            'selected_menu_color' => '#fff',
            'menu_color' => '#333',
            'border_bg' => '#ebd5fa',
            //'content_color' => '#ccc',
            //'content_border' => '#999',
            'footer_color' => '#fff',  
            'footer_link_color' => '#069', 
            'content_link_color' => '#069', 
            'snippet_color' => '#333',
            'date_color' => '#666',
        ));
    }    
}
