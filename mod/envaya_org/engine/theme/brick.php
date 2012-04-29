<?php

class Theme_Brick extends Theme_UserSite
{
    static $css = 'topmenu3';    
    static $thumbnail = '/_media/images/brick/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:brick');
    }               
    
    static function get_vars()
    {
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'default' => 'background:brick',
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
            'header_color' => array(       
                'default' => '#fff'
            ),
            'footer_color' => array(
                'default' => '#fff',
            ),        
            'subheader_bg' => array(
                'default' => 'section_header:dark_gray',
            ),
            'header_bg' => array(
                'default' => '#2a2a2a',
                'selector' => '.heading_container .thin_column',
            ),
            'border_bg' => array(
                'default' => '#2a2a2a',
            ),
            'footer_bg' => array(
                'default' => '#2a2a2a',
            ),
            'translate_bg' => array(
                'default' => '#764c40',
            ),
            'translate_border' => array(
                'default' => '#7f7f7f',
            ),
        ));
    }
}
