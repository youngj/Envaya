<?php

class Theme_LeftMenuBrown extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenubrown.png';

    static function get_display_name()
    {
        return __('design:theme:brown');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#C19A6B',
            'header_color' => '#111',
            'tagline_color' => '#111',
            'subheader_bg' => '#C19A6B',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#C19A6B',
            'menu_hover_color' => '#1f66a5',
            'selected_menu_color' => '#111',
            'menu_color' => '#333',
            'border_bg' => '#f8eee3',
            //'content_color' => '#ccc',
            //'content_border' => '#999',
            'footer_color' => '#333',  
            'footer_link_color' => '#069', 
            'content_link_color' => '#1f66a5', 
            'snippet_color' => '#333',
            'date_color' => '#666',
            'box_shadow' => 'box_shadow:black',
        ));
    }
}
