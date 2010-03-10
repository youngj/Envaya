
<div class='instructions'><?php echo elgg_echo("browse:instructions") ?></div>
<?php     
    $lat = $vars['lat'] ?: -6.6;
    $long = $vars['long'] ?: 36;
    $zoom = $vars['zoom'] ?: 5;
        
    echo elgg_view("org/map", array('lat' => $lat, 'long' => $long,  'height' => 350, 'zoom' => $zoom, 'nearby' => true));
?>    

    