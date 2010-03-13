<?php

    $widget = $vars['widget'];
    
    $content = view_translated($widget, 'content');
    
    echo elgg_view_layout('section', elgg_echo("org:mission"), $content);        
    
    $org = $vars['widget']->getContainerEntity();
        
    $posts = $org->listNewsUpdates(5, false);

    if (!$posts)
    {
        $posts = elgg_echo("org:noupdates");
    }
    else
    {
        $posts .= "<a class='float_right' href='".$org->getUrl()."/news'>View all updates</a>";
    }

    echo elgg_view_layout('section', elgg_echo("org:updates"), $posts);        

    $sectors = $org->getSectors();

    if (!empty($sectors))
    {        
        sort($sectors);
    
        $sectorOptions = Organization::getSectorOptions();
        $sectorNames = array();

        foreach ($sectors as $sector)
        {            
            $sectorNames[] = "<a href='org/search?sector=$sector'>".escape($sectorOptions[$sector])."</a>";
        }
        
        $sectorText = implode(', ', $sectorNames);               

        if (in_array(SECTOR_OTHER, $sectors))
        {
            $sectorText .= " (".escape($org->sector_other).")";
        }
        
        echo elgg_view_layout('section', elgg_echo("org:sectors"), $sectorText);        
    }   

?>
