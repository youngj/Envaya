<?php

/* 
 * A widget that displays the latest activity by an organization (FeedItem objects).
 */
class Widget_Updates extends Widget
{
    static $default_menu_order = 110;
    static $default_widget_name = 'updates';    
    
    function get_default_title()
    {
        return __("widget:updates");
    }

    function render_view($args = null)
    {
        return view("widgets/updates_view", array('widget' => $this));
    }

    function render_edit()
    {
        return view("widgets/updates_edit", array('widget' => $this));
    }

    function process_input($action)
    {
        $this->save();
    }    
}
