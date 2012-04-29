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
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'default' => 'background:beads',
            ),
            'header_bg' => array(
                'default' => 'background:wood',
            ),
            'tagline_color' => array(
                'default' => '#cca954',
            ),
            'content_bg' => array(
                'default' => 'background:light_pink_gradient',
            ),
            'selected_menu_bg' => array(
                'default' => '#d1b26c',
            ),
            'selected_menu_color' => array(
                'default' => '#000',
            ),  
            'menu_color' => array(
                'default' => '#fff',
            ),
            'content_color' => array(
                'default' => '#333',
            ),
            'subheader_color' => array(
                'default' => '#fff',
            ),        
            'header_color' => array(       
                'default' => '#fff'
            ),
            'footer_color' => array(
                'default' => '#fff',
            ),        
            'subheader_bg' => array(
                'default' => 'section_header:purple',
            ),
            'border_bg' => array(
                'default' => '#090503',
            ),
            'footer_bg' => array(
                'default' => '#090503',
            ),
            'translate_bg' => array(
                'default' => '#4e2537',
            ),
            'translate_border' => array(
                'default' => '#a5a180',
            ),
        ));
    }
}
