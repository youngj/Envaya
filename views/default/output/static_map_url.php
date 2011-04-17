<?php
    $lat = $vars['lat'];
    $long = $vars['long'];
    $zoom = $vars['zoom'];
    $width = $vars['width'];
    $height = $vars['height'];
    
    $apiKey = Config::get('google_api_key');
    echo "http://maps.google.com/maps/api/staticmap?center={$lat},{$long}&zoom={$zoom}&size={$width}x{$height}&maptype=roadmap&markers={$lat},{$long}&sensor=false&key={$apiKey}";
