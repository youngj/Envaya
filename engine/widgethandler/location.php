<?php
class WidgetHandler_Location extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/location_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return view("widgets/location_edit", array('widget' => $widget));
    }
    
    function view_feed($widget)
    {
        return view("feed/location", array('widget' => $widget));        
    }
    
    function save($widget)
    {
        $org = $widget->get_root_container_entity();

        $org->set_lat_long(get_input('org_lat'), get_input('org_lng'));

        $new_region = get_input('region');
        if ($new_region != $org->region)
        {
            $org->region = $new_region;
        }
        $org->city = get_input('city');

        $org->save();

        $widget->zoom = get_input('map_zoom');
        $widget->save();    
    }
}
