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
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'default' => 'background:craft1',
            ),
            'header_bg' => array(
                'default' => '#121d27',
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
                'default' => '#be2016',
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
                'default' => '#021019',
            ),
            'footer_bg' => array(
                'default' => '#021019',
            ),
            'translate_bg' => array(
                'default' => '#0f2129',
            ),
            'translate_border' => array(
                'default' => '#bb0f07',
            )
        ));
    }
}
