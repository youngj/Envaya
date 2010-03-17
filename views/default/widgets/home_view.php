<?php

    $widget = $vars['widget'];
    
    $content = view_translated($widget, 'content');
    
    echo elgg_view_layout('section', elgg_echo("org:mission"), $content);        
    
    $org = $vars['widget']->getContainerEntity();
    
    $posts = $org->listNewsUpdates(3, false); // make this configurable?    
    if (!$posts)
    {
        $posts = elgg_echo("org:noupdates");
    }
    else
    {
        $posts .= "<a class='float_right' href='".$org->getUrl()."/news'>".elgg_echo('blog:view_all')."</a>";
    }    

    echo elgg_view_layout('section', elgg_echo("org:news:latest"), $posts);        

    $sectors = $org->getSectors();

    if (!empty($sectors))
    {        
        sort($sectors);
    
        $sectorOptions = Organization::getSectorOptions();
        $sectorNames = array();

        foreach ($sectors as $sector)
        {            
            $sectorNames[] = "<a href='org/browse?list=1&sector=$sector'>".escape($sectorOptions[$sector])."</a>";
        }
        
        $sectorText = implode(', ', $sectorNames);               

        if (in_array(SECTOR_OTHER, $sectors) && $org->sector_other)
        {
            $sectorText .= " (".escape($org->sector_other).")";
        }
        
        echo elgg_view_layout('section', elgg_echo("org:sectors"), $sectorText);        
    }   
    
    foreach ($widget->included as $includedWidget)
    {
        $included = $org->getWidgetByName($includedWidget);        
        if ($included->isActive())
        {
            echo "<div class='section_header' style='margin-bottom:3px'>".elgg_echo("widget:{$included->widget_name}")."</div>";
            echo $included->renderView();        
        }    
    }

?>
