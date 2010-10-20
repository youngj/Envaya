<?php
class WidgetHandler_Home extends WidgetHandler
{
    function view($widget)
    {
        PageContext::set_rss(true);
        
        return view("widgets/home_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return view("widgets/home_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        $lastUpdated = $widget->time_updated;
        $org = $widget->get_container_entity();

        $mission = get_input('content');
        if (!$mission)
        {
            throw new InvalidParameterException(__("setup:mission:blank"));
        }

        $sectors = get_input_array('sector');
        if (sizeof($sectors) == 0)
        {
            throw new InvalidParameterException(__("setup:sector:blank"));
        }
        else if (sizeof($sectors) > 5)
        {
            throw new InvalidParameterException(__("setup:sector:toomany"));
        }

        $org->set_sectors($sectors);
        $org->sector_other = get_input('sector_other');

        $org->latitude = get_input('org_lat');
        $org->longitude = get_input('org_lng');

        $org->region = get_input('region');
        $org->city = get_input('city');

        $org->save();

        $widget->set_content($mission, true);

        $widget->included = get_input_array('included');
        $widget->zoom = get_input('map_zoom');
        $widget->save();
        
        if (!Session::isadminloggedin() && time() - $lastUpdated > 86400)
        {
            post_feed_items($widget->get_container_entity(), 'edithome', $widget);
        }        
    }
}
