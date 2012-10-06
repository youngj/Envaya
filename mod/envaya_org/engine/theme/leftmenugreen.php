<?php

class Theme_LeftMenuGreen extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenugreen.png';

    static function get_display_name()
    {
        return __('design:theme:green');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#035415',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#035415',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#035415',
            'menu_hover_color' => '#1f66a5',
            'selected_menu_color' => '#fff',
            'menu_color' => '#333',
            'border_bg' => '#f0fff0',
            //'content_color' => '#ccc',
            //'content_border' => '#999',
            'footer_color' => '#fff',  
            'footer_link_color' => '#39f', 
            'content_link_color' => '#1f66a5', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => 'box_shadow:black',
        ));
    }    
}
