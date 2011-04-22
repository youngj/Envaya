<?php

/* 
 * A container widget that displays child widgets as an ordered list of sections.
 * This is created by default as the organization's home page, with sections 
 * of types Widget_Mission, Widget_Updates, Widget_Sectors, and Widget_Location.
 */
class Widget_Home extends Widget
{
    function get_default_subtitle()
    {
        $org = $this->get_root_container_entity();
        return $org->get_location_text(false);    
    }
   
    function render_view($args = null)
    {
        return view("widgets/home_view", array('widget' => $this));
    }

    function render_edit()
    {
        return view("widgets/home_edit", array('widget' => $this));
    }

    function process_input($action)
    {
        $this->save();
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
        return $this->get_widget_by_name(get_input('uniqid'));
    }
}