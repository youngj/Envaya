<?php
    require_once "start.php";      
    
    $themes_map = array(
        'green' => 'Theme_LightBlue',
        'sidebar' => 'Theme_LeftMenu',
        'beads' => 'Theme_Beads',
        'brick' => 'Theme_Brick',
        'cotton2' => 'Theme_Cotton',
        'craft1' => 'Theme_Craft1',
        'craft4' => 'Theme_Craft4',
        'wovengrass' => 'Theme_WovenGrass',
        'red' => 'Theme_Chrome',
        'personprofile' => 'Theme_PersonProfile',
    );
    
    $users = User::query()->filter();
    foreach ($users as $user)
    {
        $theme_name = $user->get_design_setting('theme_name');
        
        $theme = $themes_map[$theme_name];
        
        if (!$theme)
        {
            error_log("unknown theme $theme_name");
        }
        else
        {        
            $user->set_design_setting('theme_id', $theme::get_subtype_id());
            $user->save();
        }
    }