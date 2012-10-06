<?php

class Theme_WovenGrass extends Theme_UserSite
{
    static $css = 'topmenu2';    
    static $thumbnail = '/_media/images/wovengrass/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:wovengrass');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => 'background:wovengrass',
            'header_bg' => '#fff',
            'header_color' => '#333',
            'tagline_color' => '#a07d28',
            'content_bg' => 'background:yellow_gradient',
            'selected_menu_bg' => '#a07d28',
            'selected_menu_color' => '#fff',
            'menu_color' => '#000',
            'content_color' => '#333',
            'subheader_color' => '#fff',
            'footer_link_color' => '#069',
            'subheader_bg' => 'section_header:brown2',
            'border_bg' => 'background:light_wovengrass',
            'footer_bg' => 'background:light_wovengrass',
            'translate_bg' => '#a68c4d',
            'translate_border' => '#f4ebc5',    
        ));
    }
}
