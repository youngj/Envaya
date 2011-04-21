<?php
class Widget_Home extends Widget
{
    function get_default_subtitle()
    {
        $org = $this->get_root_container_entity();
        return $org->get_location_text(false);    
    }
   
    function render_view()
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
}