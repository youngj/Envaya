<?php

class Theme_Chrome extends Theme_UserSite
{
    static $css = 'chrome';    
    static $thumbnail = '/_media/images/chrome/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:chrome');
    }               
    
    static function get_vars()
    {
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(
                'type' => 'background',
                'selector' => 'body',                    
                'default' => '#e7e2d7',
            ),
            'content_bg' => array(
                'hidden' => true,
            ),
            'selected_menu_bg' => array(
                'hidden' => true,
            ),
            'selected_menu_color' => array(
                'default' => '#000',
            ),  
            'menu_color' => array(
                'default' => '#4690D6',
            ),
            'content_color' => array(
                'default' => '#333',
            ),
            'subheader_color' => array(
                'default' => '#000',
            ),        
            'header_color' => array(       
                'default' => '#000'
            ),
            'footer_color' => array(
                'default' => '#000',
            ),        
            'subheader_bg' => array(
                'hidden' => true,
            ),
            'header_bg' => array(
                'hidden' => true,
            ),
            'border_bg' => array(
                'hidden' => true,
            ),
            'footer_bg' => array(
                'hidden' => true,
            ),
        ));
    }
}
