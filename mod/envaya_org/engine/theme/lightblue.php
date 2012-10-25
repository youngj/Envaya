<?php

class Theme_LightBlue extends Theme_UserSite
{
    static $css = 'lightblue';
    static $thumbnail = '/_media/images/lightblue/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:basic');
    }           
    
    static function get_types()
    {
        return array_merge(parent::get_types(), array(
            'selected_menu_background' => "SELECTOR.selected, SELECTOR:hover { background:VALUE; background-position:left top; }\n".
                    "SELECTOR.selected span, SELECTOR:hover span { background:VALUE; background-position:right top; }",
        ));
    }
    
    static function get_vars()
    {
        return static::set_defaults(parent::get_vars(), array(  
            'body_bg' => '#fff',            
            'header_bg' => '#f2f5f6',
            'main_bg' => 'background:gradient',
            'content_bg' => 'background:gradient2',
            'header_color' => '#000',
            'subheader_bg' => 'section_header:blue',
            'selected_menu_bg' => 'menu_button:blue',
            'border_bg' => 'background:gradient3',
        ));
    }
}
