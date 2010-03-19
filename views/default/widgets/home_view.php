<?php

    $widget = $vars['widget'];
    
    $content = view_translated($widget, 'content');
    
    echo elgg_view_layout('section', elgg_echo("org:mission"), $content);        
    
    $org = $vars['widget']->getContainerEntity();
    
    echo "<div class='section_header'>".elgg_echo("org:news:latest")."</div>";

    
    $posts = $org->listNewsUpdates(3, false); // make this configurable?    
    
    if (empty($posts))
    {
        echo "<div class='padded'>".elgg_echo("org:noupdates")."</div>";
    }
    else
    {
        echo $posts;
        echo "<div style='padding:5px'><a class='float_right' href='".$org->getUrl()."/news'>".elgg_echo('blog:view_all')."</a><div style='clear:both'></div></div>";
    }    
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
