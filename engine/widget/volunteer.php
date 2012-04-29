<?php

class Widget_Volunteer extends Widget_Generic
{
    static $default_menu_order = 55;
    static $default_widget_name = 'volunteer';    
    
    function get_default_title()
    {
        return __("widget:volunteer");
    }

    function get_edit_heading()
    {
        return __('widget:volunteer:label');
    }
    
    function get_edit_help()
    {
        return __('widget:volunteer:help');
    }        
}