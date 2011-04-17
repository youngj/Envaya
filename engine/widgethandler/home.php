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
            return redirect_back_error(__("setup:mission:blank"));
        }

        $sectors = get_input_array('sector');
        if (sizeof($sectors) == 0)
        {
            return redirect_back_error(__("setup:sector:blank"));
        }
        else if (sizeof($sectors) > 5)
        {
            return redirect_back_error(__("setup:sector:toomany"));
        }

        $old_sectors = $org->get_sectors();
        sort($old_sectors);
        sort($sectors);
        
        // optimization to avoid dirtying fields that would force sphinx reindex
        if ($old_sectors !== $sectors)
        {        
            $org->set_sectors($sectors);
        }
        $org->sector_other = get_input('sector_other');

        $org->set_lat_long(get_input('org_lat'), get_input('org_lng'));

        $new_region = get_input('region');
        if ($new_region != $org->region)
        {
            $org->region = $new_region;
        }
        $org->city = get_input('city');

        $org->save();

        $widget->set_content($mission);

        $widget->included = get_input_array('included');
        $widget->zoom = get_input('map_zoom');
        $widget->save();
        
        if (!Session::isadminloggedin() && time() - $lastUpdated > 86400)
        {
            FeedItem::post($widget->get_container_entity(), 'edithome', $widget);
        }        
    }
}
