<?php

class Theme_Craft1 extends Theme_UserSite
{
    static $css = 'topmenu2';    
    static $thumbnail = '/_media/images/craft/thumbnail1.png';

    static function get_display_name()
    {
        return __('design:theme:craft1');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => 'background:craft1',
            'header_bg' => '#121d27',
            'header_color' => '#fff',
            'tagline_color' => '#dbc777',
            'content_bg' => 'background:beige_gradient',
            'selected_menu_bg' => '#be2016',
            'selected_menu_color' => '#fff',
            'menu_color' => '#fff',
            'content_color' => '#333',
            'subheader_color' => '#fff',
            'footer_color' => '#fff',
            'subheader_bg' => 'section_header:brown',
            'border_bg' => '#021019',
            'footer_bg' => '#021019',
            'translate_bg' => '#0f2129',
            'translate_border' => '#bb0f07',
        ));
    }
}
