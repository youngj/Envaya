<?php

class Theme_LeftMenu extends Theme_UserSite
{
    static $css = 'leftmenu';    
    static $layout = 'layouts/two_column_left_sidebar';
    static $thumbnail = '/_media/images/leftmenu/thumbnail.png';

    static function get_display_name()
    {
        return __('design:theme:leftmenu');
    }               
    
    static function get_vars()
    {
        return static::merge_vars(parent::get_vars(), array(
            'body_bg' => array(          
                'default' => '#fafafa',
            ),        
            'header_color' => array(       
                'default' => '#000'
            ),
            'tagline_color' => array(       
                'type' => 'text_color',
                'selector' => '#heading h3',
                'default' => '#000'
            ),        
            'subheader_bg' => array(
                'default' => '#f0f0f0',
            ),
            'selected_menu_bg' => array(       
                'type' => 'background',        
                'selector' => '#site_menu a.selected',        
                'default' => 'left_menu_background:gray',
            ),
            'selected_menu_color' => array(
                'default' => '#000',
            ),
            'menu_color' => array(       
                'default' => '#8c8b8b',
            ),
            'border_bg' => array(
                'type' => 'background',
                'selector' => '#content_wrapper',        
                'default' => '#fdfdfd',
            ),
        ));
    }
}
