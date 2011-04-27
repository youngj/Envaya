<?php

/* 
 * A widget that displays an organization's sectors (see OrgSector)
 * storing the sectors on the organization itself so they can be easily queried.
 */
class Widget_Sectors extends Widget
{
    function render_view($args = null)
    {
        return view("widgets/sectors_view", array('widget' => $this));
    }
    
    function render_edit()
    {
        return view("widgets/sectors_edit", array('widget' => $this));
    }
    
    function render_view_feed()
    {
        return $this->render_view();
    }
    
    function process_input($action)
    {
        $org = $this->get_root_container_entity();
    
        $sectors = get_input_array('sector');
        if (sizeof($sectors) == 0)
        {
            return redirect_back_error(__("register:sector:blank"));
        }
        else if (sizeof($sectors) > 5)
        {
            return redirect_back_error(__("register:sector:toomany"));
        }

        $old_sectors = $org->get_sectors();
        sort($old_sectors);
        sort($sectors);
        
        // optimization to avoid dirtying fields that would force sphinx reindex
        if ($old_sectors !== $sectors)
        {        
            $org->set_sectors($sectors);
        }
        
        $org->set_metadata('sector_other', get_input('sector_other'));
        $org->save();
        
        $this->save();
    }
}
