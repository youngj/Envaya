<?php

class Theme_LeftMenu extends Theme_UserSite
{
    static $css = 'leftmenu';    
    static $layout = 'layouts/two_column_left_sidebar';
    static $thumbnail = '/_media/images/leftmenugray.png';

    static function get_display_name()
    {
        return __('design:theme:light');
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
            'box_shadow' => array(
                'selector' => '#content_wrapper',
                'type' => 'box_shadow',
                'default' => 'box_shadow:gray',
            ),
            'subheader_bg' => array(
                'default' => '#f0f0f0',
            ),
            'selected_menu_bg' => array(       
                'type' => 'background',        
                'selector' => '#site_menu a.selected',                        
                'default' => '#f0f0f0',
            ),
            'selected_menu_color' => array(
                'selector' => '#site_menu a.selected',        
                'default' => '#000',
            ),
            'menu_hover_color' => array(
                'type' => 'text_color',
                'selector' => '#site_menu a.notselected:hover',        
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
            'content_border' => array(
                'type' => 'border_color',
                'selector' => '#content_wrapper, #left_sidebar', 
                'default' => '#e8e8e8',
            ),
            'content_color' => array(       
                'selector' => '.left_sidebar_table',        
            ),       
            'content_link_color' => array(
                'selector' => '.left_sidebar_table a',        
            ),                        
            'footer_color' => array(       
                'selector' => '.footer_container',        
            ),       
            'footer_link_color' => array(
                'selector' => '.footer_container a',        
            ),            
        ));
    }        
}
