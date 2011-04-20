<?php

class WidgetHandler_Sectors extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/sectors_view", array('widget' => $widget));
    }
    
    function edit($widget)
    {
        return view("widgets/sectors_edit", array('widget' => $widget));
    }
    
    function view_feed($widget)
    {
        return $this->view($widget);
    }
    
    function save($widget)
    {
        $org = $widget->get_root_container_entity();
    
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
        $org->save();
        
        $widget->save();
    }
}
