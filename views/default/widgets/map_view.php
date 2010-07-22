<div class='padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
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
    echo __('widget:map:see_nearby');
    echo "</a>";
    echo "</div>";
?>
</div>