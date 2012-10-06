<?php

class Theme_LightGray extends Theme_Solid
{
    static $thumbnail = '/_media/images/lightgray.png';

    static function get_display_name()
    {
        return __('design:theme:light');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => '#f0f0f0',
            'header_color' => '#000',
            'tagline_color' => '#000',
            'subheader_bg' => '#ccc',
            'subheader_color' => '#333',
            'selected_menu_bg' => '#ccc',
            'selected_menu_color' => '#000',
            'menu_color' => '#333',
            'content_border' => '#ccc',
            'content_bg' => '#fbfbfb',
            //'footer_color' => '#fff',  
            //'footer_link_color' => '#39f', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => 'box_shadow:gray',
        ));
    }
}
