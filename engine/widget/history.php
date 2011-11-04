<?php

class Widget_History extends Widget_Generic
{
    static $default_menu_order = 40;
    static $default_widget_name = 'history';    
    
    function get_default_title()
    {
        return __("widget:history");
    }
    
    function get_edit_heading()
    {
        return __('widget:history:label');
    }
    
    function get_edit_help()
    {
        return __('widget:history:help');
    }    
}