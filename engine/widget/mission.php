<?php

/* 
 * A widget that displays the mission statement of an organization.
 */
class Widget_Mission extends Widget_Generic
{
    static $default_menu_order = 100;
    static $default_widget_name = 'mission';    
    
    function get_default_title()
    {
        return __("widget:mission");
    }

    function get_edit_heading()
    {
        return __('widget:mission:label');
    }    

    function process_input($action)
    {    
        $mission = Input::get_string('content');
        if (!$mission)
        {
            throw new ValidationException(__("register:mission:blank"));
        }    
        parent::process_input($action);
    }
}
