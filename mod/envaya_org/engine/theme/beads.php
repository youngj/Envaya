<?php

class Theme_Beads extends Theme_UserSite
{
    static $css = 'topmenu';    
    static $thumbnail = '/_media/images/beads/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:beads');
    }               
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(
            'body_bg' => 'background:beads',
            'header_bg' => 'background:wood',
            'tagline_color' => '#cca954',
            'content_bg' => 'background:light_pink_gradient',
            'selected_menu_bg' => '#d1b26c',
            'selected_menu_color' => '#000',
            'menu_color' => '#fff',
            'content_color' => '#333',
            'subheader_color' => '#fff',
            'header_color' => '#fff',
            'footer_color' => '#fff',
            'subheader_bg' => 'section_header:purple',
            'border_bg' => '#090503',
            'footer_bg' => '#090503',
            'translate_bg' => '#4e2537',
            'translate_border' => '#a5a180',
        ));
    }
}
