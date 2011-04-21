<?php
class Widget_Location extends Widget
{
    function render_view()
    {
        return view("widgets/location_view", array('widget' => $this));
    }

    function render_edit()
    {
        return view("widgets/location_edit", array('widget' => $this));
    }
    
    function render_view_feed()
    {
        return view("feed/location", array('widget' => $this));        
    }
    
    function process_input($action)
    {
        $org = $this->get_root_container_entity();

        $org->set_lat_long(get_input('org_lat'), get_input('org_lng'));

        $new_region = get_input('region');
        if ($new_region != $org->region)
        {
            $org->region = $new_region;
        }
        $org->city = get_input('city');

        $org->save();

        $this->zoom = get_input('map_zoom');
        $this->save();    
    }
}
