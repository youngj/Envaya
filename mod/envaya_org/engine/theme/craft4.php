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
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'default' => 'background:craft4',
            ),
            'header_bg' => array(
                'default' => 'background:wood2',
            ),
            'header_color' => array(
                'default' => '#fff',
            ),
            'tagline_color' => array(
                'default' => '#dbc777',
            ),
            'content_bg' => array(
                'default' => 'background:beige_gradient',
            ),
            'selected_menu_bg' => array(
                'default' => '#641d09',
            ),
            'selected_menu_color' => array(
                'default' => '#fff',
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
            'footer_color' => array(
                'default' => '#fff',
            ),        
            'subheader_bg' => array(
                'default' => 'section_header:brown',
            ),
            'border_bg' => array(
                'default' => '#916c4c',
            ),
            'footer_bg' => array(
                'default' => '#916c4c',
            ),
            'translate_bg' => array(
                'default' => '#641d09',
            ),
            'translate_border' => array(
                'default' => '#e36306',
            ),
        ));
    }
}
