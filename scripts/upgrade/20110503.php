<?php

    require_once("scripts/cmdline.php");
    require_once("start.php");
    
    foreach (Organization::query()
        ->where('design_json is null or design_json = ?','')
        ->filter() as $org)
    {
        echo "{$org->name}\n";
        $org->set_design_setting('theme_name', $org->theme);
        
        if ($org->username != 'envaya')
        {
            $org->set_design_setting('tagline', $org->get_location_text(false));
        }
        
        $org->set_design_setting('share_links', array('email','facebook','twitter'));
        
        if ($org->header_json)
        {
            $org->set_design_setting('custom_header', 1);
            $org->set_design_setting('header_image', json_decode($org->header_json, true));
        }
        else
        {
            $org->set_design_setting('custom_header', 0);
        }
        
        $org->save();
    }
    
    foreach (OrgRelationship::query()->where('approval = 2')->filter() as $relationship)
    {
        $relationship->delete();
    }