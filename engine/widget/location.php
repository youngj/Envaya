<?php

/* 
 * A widget that displays the location of an organization on a map, storing the 
 * latitude/longitude on the Organization itself so it can be easily queried.
 */
class Widget_Location extends Widget
{
    static $default_menu_order = 130;
    static $default_widget_name = 'location';    
    
    function get_default_title()
    {
        return __("widget:location");
    }

    function render_view($args = null)
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
        $org = $this->get_container_user();

        $org->set_lat_long(get_input('lat'), get_input('long'));

        $new_region = get_input('region');
        if ($new_region != $org->region)
        {
            $org->region = $new_region;
        }
        $org->city = get_input('city');

        $org->save();

        $this->set_metadata('zoom', get_input('zoom'));
        $this->save();    
    }
}
