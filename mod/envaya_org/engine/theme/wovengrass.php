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
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'default' => 'background:wovengrass',
            ),
            'header_bg' => array(
                'default' => '#fff',
            ),
            'header_color' => array(
                'default' => '#333',
            ),
            'tagline_color' => array(
                'default' => '#a07d28',
            ),
            'content_bg' => array(
                'default' => 'background:yellow_gradient',
            ),
            'selected_menu_bg' => array(
                'default' => '#a07d28',
            ),
            'selected_menu_color' => array(
                'default' => '#fff',
            ),  
            'menu_color' => array(
                'default' => '#000',
            ),
            'content_color' => array(
                'default' => '#333',
            ),
            'subheader_color' => array(
                'default' => '#fff',
            ),        
            'footer_link_color' => array(
                'default' => '#069',
            ),        
            'subheader_bg' => array(
                'default' => 'section_header:brown2',
            ),
            'border_bg' => array(
                'default' => 'background:light_wovengrass',
            ),
            'footer_bg' => array(
                'default' => 'background:light_wovengrass',
            ),
            'translate_bg' => array(
                'default' => '#a68c4d',
            ),
            'translate_border' => array(
                'default' => '#f4ebc5',
            ),                        
        ));
    }
}
