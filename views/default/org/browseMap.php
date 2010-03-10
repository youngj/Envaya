
<div class='instructions'><?php echo elgg_echo("browse:instructions") ?></div>
<?php     
    $lat = $vars['lat'] ?: -6.6;
    $long = $vars['long'] ?: 36;
        
    echo elgg_view("org/map", array('lat' => $lat, 'long' => $long,  'height' => 350, 'zoom' => 5, 'nearby' => true));
?>    

    