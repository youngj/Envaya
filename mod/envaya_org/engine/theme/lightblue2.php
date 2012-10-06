<?php

class Theme_LightBlue2 extends Theme_Solid
{
    static $thumbnail = '/_media/images/lightblue.png';

    static function get_display_name()
    {
        return __('design:theme:lightblue');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#cee1f2',
            'header_color' => '#000',
            'tagline_color' => '#000',
            'subheader_bg' => '#cee1f2',
            'subheader_color' => '#333',
            'selected_menu_bg' => '#eef0ff',
            'selected_menu_color' => '#000',
            'menu_color' => '#333',
            'content_border' => '#fff',
            'content_bg' => '#eef0ff',
            //'footer_color' => '#fff',  
            //'footer_link_color' => '#39f', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => '',
        ));
    }
}
