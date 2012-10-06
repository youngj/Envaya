<?php

class Theme_LeftMenuLightBlue extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenulightblue.png';

    static function get_display_name()
    {
        return __('design:theme:lightblue');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#cee1f2',
            //'header_color' => '#fff',
            //'tagline_color' => '#fff',
            'subheader_bg' => '#cee1f2',
            //'subheader_color' => '#fff',
            'selected_menu_bg' => '#cee1f2',
            'menu_hover_color' => '#1f66a5',
            //'selected_menu_color' => '#fff',
            'menu_color' => '#333',
            'border_bg' => '#eef0ff',
            //'content_color' => '#ccc',
            //'content_border' => '#999',
            //'footer_color' => '#fff',  
            'footer_link_color' => '#1f66a5', 
            'content_link_color' => '#1f66a5', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => 'box_shadow:black',
        ));
    }    
}
