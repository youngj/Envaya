<?php

/* 
 * A container widget that displays child widgets as an ordered list of sections.
 * This is created by default as the organization's home page, with sections 
 * of types Widget_Mission, Widget_Updates, Widget_Sectors, and Widget_Location.
 */
class Widget_Home extends Widget_Generic
{
    static $default_menu_order = 10;
    static $default_widget_name = 'home';    
    
    function get_default_title()
    {
        return __("widget:home");
    }
    
    function get_default_widget_class_for_name($widget_name)
    {
        return Widget::get_default_class_for_name($widget_name, 'home_section')
            ?: 'Widget_Generic';
    }        

    function get_view_types()
    {
        return array('rss');
    }
   
    function render_view($args = null)
    {
        return view("widgets/home_view", array('widget' => $this));
    }

    function render_edit()
    {
        return view("widgets/home_edit", array('widget' => $this));
    }
    
    function is_section_container()
    {
        return true;
    }    
    
    function render_add_child()
    {
        return view("widgets/add_section", array('widget' => $this));
    }
    
    function new_child_widget_from_input()
    {        
        $uniqid = Input::get_string('uniqid');
        return $this->get_widget_by_name($uniqid)
            ?: Widget_Generic::new_for_entity($this, array('widget_name' => $uniqid));
    }
}