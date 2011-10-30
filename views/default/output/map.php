<?php
    $id = "map$INCLUDE_COUNT";      // DOM id for container element
    $width = 570;                   // container width
    $height = 370;                  // container height
    $onload = null;                 // JavaScript callback, function(map) { .. } 
    $zoom = 10;                     // initial zoom level
    $map_type = "ROADMAP";          // initial google map type constant
    $lat = null;                    // initial center latitude
    $long = null;                   // initial center longitude
    $pin = false;                   // put a pin at the initial latitude/longitude?
    $static = false;                // true to use static map instead of JS map
    extract($vars);
    
    $style = "display:block;margin:0 auto;width:{$width}px;height:{$height}px";
    
    if ($static)
    {
        echo Markup::empty_tag('img', array(
            'id' => $id,
            'src' => Geography::get_static_map_url($vars),
            'style' => $style,
        ));
    }
    else
    {
        echo Markup::start_tag('div', array(
            'id' => $id,
            'style' => $style,
        ));
        echo Markup::end_tag('div');
        
        $lang = Language::get_current_code();        
        echo "<script type='text/javascript' src='//maps.google.com/maps/api/js?sensor=false&language={$lang}'></script>";       
?>
<script type="text/javascript">
google.maps.event.addDomListener(window, 'load', function()
{
    var center = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
    var map = new google.maps.Map($("<?php echo $id; ?>"), {
        center: center,
        zoom: <?php echo $zoom; ?>,
        mapTypeId: google.maps.MapTypeId.<?php echo $map_type; ?>,
        mapTypeControl: false,
        streetViewControl: false,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL
        },
        panControl: true
    });
    
    <?php 
    if ($pin)
    {
        echo "map.addOverlay(new GMarker(center));";
    }
    
    if ($onload) 
    {
        echo "$onload(map)";
    }
    ?>
});
</script>

<?php
    } // end JS map
?>
