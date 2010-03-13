<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
    $lat = $org->getLatitude() ?: 0.0;
    $long = $org->getLongitude() ?: 0.0;
    
    $zoom = ($lat || $long) ? 11 : 1;    
   
    $content = elgg_view("org/map", array(
        'lat' => $lat, 
        'long' => $long,
        'zoom' => $zoom,
        'pin' => true,
        'org' => $group,
        'edit' => true
    ));   
   
    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
    
?>
