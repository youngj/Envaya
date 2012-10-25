<?php

class Theme_LeftMenuSage extends Theme_LeftMenu
{
    static $thumbnail = '/_media/images/leftmenusage.png';

    static function get_display_name()
    {
        return __('design:theme:sage');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#BCB88A',
            'header_color' => '#111',
            'tagline_color' => '#111',
            'subheader_bg' => '#BCB88A',
            'subheader_color' => '#111',
            'selected_menu_bg' => '#BCB88A',
            'menu_hover_color' => '#1f66a5',
            'selected_menu_color' => '#111',
            'menu_color' => '#333',
            'border_bg' => '#fbfdeb',
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
