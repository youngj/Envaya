<?php
    $lat_id = "latitude_{$INCLUDE_COUNT}";
    $long_id = "longitude_{$INCLUDE_COUNT}";
    $zoom_id = "zoom_{$INCLUDE_COUNT}";
    
    $lat_name = null;
    $long_name = null;
    $zoom_name = null;
    
    $lat = null;
    $long = null;
    $zoom = null;

    $width = 560;
    $height = 350;
    
    extract($vars);
    
    if (!$lat)
    {
        $lat = 0.0;
    }
    
    if (!$long)
    {
        $long = 0.0;
    }       
    
    if (!$zoom)
    {
        $zoom = ($lat || $long) ? 11 : 1;
    }    
?>

<script type='text/javascript'>

function <?php echo "initMapInput$INCLUDE_COUNT"; ?>(map)
{
    function updateHiddenFields()
    {
        var pos = marker.getPosition();
        $(<?php echo json_encode($lat_id); ?>).value = pos.lat();
        $(<?php echo json_encode($long_id); ?>).value = pos.lng();
        $(<?php echo json_encode($zoom_id); ?>).value = map.getZoom();
    }

    var marker = new google.maps.Marker({
        position: map.getCenter(), 
        draggable: true
    });

    google.maps.event.addListener(marker, "dragend", function(e) {
        map.setCenter(e.latLng);
        updateHiddenFields();
    });

    google.maps.event.addListener(map, "zoom_changed", function() {
        updateHiddenFields();
    });

    marker.setMap(map);
}

</script>

<?php
    echo view('input/hidden', array('id' => $lat_id, 'name' => $lat_name, 'value' => $lat));
    echo view('input/hidden', array('id' => $long_id, 'name' => $long_name, 'value' => $long));
    echo view('input/hidden', array('id' => $zoom_id, 'name' => $zoom_name, 'value' => $zoom));

    echo view("output/map", array(
        'lat' => $lat,
        'long' => $long,
        'zoom' => $zoom,
        'width' => $width,
        'height' => $height,        
        'onload' => "initMapInput{$INCLUDE_COUNT}",
    ));
