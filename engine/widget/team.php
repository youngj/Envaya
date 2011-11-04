<?php

/* 
 * A widget that displays an organization's team members - a free-text HTML page
 * with a simple UI for adding another member.
 */
class Widget_Team extends Widget_Generic
{
    static $default_menu_order = 50;
    static $default_widget_name = 'team';    
    
    function get_default_title()
    {
        return __("widget:team");
    }

    function get_edit_heading()
    {
        return __('widget:team:label');
    }
    
    function get_edit_help()
    {
        return __('widget:team:help');
    }            
    
    function render_edit()
    {
        return view("widgets/team_edit", array('widget' => $this));
    }
}
