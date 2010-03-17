<div class='padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
    $zoom = $widget->zoom ?: 10;
    
    $entityLat = $org->getLatitude();
    echo elgg_view("org/map", array(
        'lat' => $entityLat, 
        'long' => $org->getLongitude(),
        'zoom' => $zoom,
        'pin' => true,
        'static' => true
    ));        
    echo "<div style='text-align:center;font-style:italic'>".escape($org->getLocationText())."</div>";
?>
</div>