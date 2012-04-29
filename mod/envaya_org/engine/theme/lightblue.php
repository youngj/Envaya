<?php

class Theme_LightBlue extends Theme_UserSite
{
    static $css = 'lightblue';
    static $thumbnail = '/_media/images/lightblue/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:lightblue');
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
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'default' => '#fff',
            ),
            'header_bg' => array(
                'default' => '#f2f5f6',
            ),
            'main_bg' => array(
                'default' => 'background:gradient',
            ),
            'content_bg' => array(
                'default' => 'background:gradient2',
            ),
            'header_color' => array(       
                'default' => '#000'
            ),
            'subheader_bg' => array(
                'default' => 'section_header:blue',
            ),
            'selected_menu_bg' => array(       
                'default' => 'menu_button:blue',
            ),
            'border_bg' => array(
                'default' => 'background:gradient3',
            ),
        ));
    }
}
