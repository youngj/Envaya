<?php

class Widget_Projects extends Widget_Generic
{
    static $default_menu_order = 30;
    static $default_widget_name = 'projects';    
    
    function get_default_title()
    {
        return __("widget:projects");
    }

    function get_edit_heading()
    {
        return __('widget:projects:label');
    }
    
    function get_edit_help()
    {
        return __('widget:projects:help');
    }        
}