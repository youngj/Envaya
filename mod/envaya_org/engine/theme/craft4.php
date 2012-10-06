<?php

class Theme_Craft4 extends Theme_UserSite
{
    static $css = 'topmenu2';    
    static $thumbnail = '/_media/images/craft/thumbnail4.png';

    static function get_display_name()
    {
        return __('design:theme:craft4');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => 'background:craft4',
            'header_bg' => 'background:wood2',
            'header_color' => '#fff',
            'tagline_color' => '#dbc777',
            'content_bg' => 'background:beige_gradient',
            'selected_menu_bg' => '#641d09',
            'selected_menu_color' => '#fff',
            'menu_color' => '#fff',
            'content_color' => '#333',
            'subheader_color' => '#fff',
            'footer_color' => '#fff',
            'subheader_bg' => 'section_header:brown',
            'border_bg' => '#916c4c',
            'footer_bg' => '#916c4c',
            'translate_bg' => '#641d09',
            'translate_border' => '#e36306',
        ));
    }
}
