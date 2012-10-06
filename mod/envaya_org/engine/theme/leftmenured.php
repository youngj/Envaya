<?php

class Theme_LeftMenuRed extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenured.png';

    static function get_display_name()
    {
        return __('design:theme:red');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#a40004',
            'header_color' => '#fff',
            'tagline_color' => '#fff',
            'subheader_bg' => '#a40004',
            'subheader_color' => '#fff',
            'selected_menu_bg' => '#a40004',
            'menu_hover_color' => '#1f66a5',
            'selected_menu_color' => '#fff',
            'menu_color' => '#333',
            'border_bg' => '#ffdcdc',
            //'content_color' => '#ccc',
            'content_border' => '#ccc',
            'footer_color' => '#fff',  
            'footer_link_color' => '#ccf', 
            'content_link_color' => '#1f66a5', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => 'box_shadow:black',
        ));
    }    
}
