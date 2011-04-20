<?php
class WidgetHandler_Home extends WidgetHandler
{
    function get_default_subtitle($widget)
    {
        $org = $widget->get_container_entity();
        return $org->get_location_text(false);    
    }
   
    function view($widget)
    {
        return view("widgets/home_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return view("widgets/home_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        $widget->save();
    }
}