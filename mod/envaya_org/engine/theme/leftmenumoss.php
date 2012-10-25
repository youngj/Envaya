<?php

class Theme_LeftMenuMoss extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenumoss.png';

    static function get_display_name()
    {
        return __('design:theme:moss');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#b5da9c',
            'header_color' => '#111',
            'tagline_color' => '#111',
            'subheader_bg' => '#b5da9c',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#b5da9c',
            'menu_hover_color' => '#1f66a5',
            'selected_menu_color' => '#111',
            'menu_color' => '#333',
            'border_bg' => '#fafffa',
            //'content_color' => '#ccc',
            //'content_border' => '#999',
            'footer_color' => '#333',  
            'footer_link_color' => '#069', 
            //'content_link_color' => '#1f66a5', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => 'box_shadow:black',
        ));
    }
}
