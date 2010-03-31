<?php

    $widget = $vars['widget'];
    
    $content = view_translated($widget, 'content');
    
    echo elgg_view_layout('section', elgg_echo("org:mission"), $content);        
    
    $org = $vars['widget']->getContainerEntity();
    
    echo "<div class='section_header'>".elgg_echo("widget:news:latest")."</div>";
        
    $posts = $org->getNewsUpdates(3); // make this configurable?    
    
    echo "<div class='section_content'>";
    
    if (empty($posts))
    {
        echo "<div class='padded'>".elgg_echo("widget:news:empty")."</div>";
    }
    else
    {
        foreach ($posts as $post)
        {
            echo elgg_view_entity($post);
        }
        echo "<div style='padding:5px'><a class='float_right' href='".$org->getUrl()."/news'>".elgg_echo('blog:view_all')."</a><div style='clear:both'></div></div>";
    }    
    
    echo "</div>";
    
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
    
    ob_start();
        $zoom = $widget->zoom ?: 10;
        
        $lat = $org->getLatitude();
        $long = $org->getLongitude();
        echo elgg_view("org/map", array(
            'lat' => $lat, 
            'long' => $long,
            'zoom' => $zoom,
            'pin' => true,
            'static' => true
        ));        
        echo "<div style='text-align:center'>";    
        echo "<em>";
        echo escape($org->getLocationText());
        echo "</em>";
        echo "<br />";    
        echo "<a href='org/browse/?lat=$lat&long=$long&zoom=10'>";
        echo elgg_echo('org:see_nearby');
        echo "</a>";
        echo "</div>";
    $map = ob_get_clean();    
    echo elgg_view_layout('section', elgg_echo("org:location"), $map);        
    
?>
