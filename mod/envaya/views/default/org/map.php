
<?php

    $zoom = $vars['zoom'] ? $vars['zoom'] : 10;
    $width = $vars['width'] ? $vars['width'] : 600;
    $height = $vars['height'] ? $vars['height'] : 400;    
    $apiKey = get_plugin_setting('google_api', 'googlegeocoder');
    $lat = $vars['lat'];
    $long = $vars['long'];
    
    if (!$vars['static'])
    {
?>

<div id='map' style='width:<?php echo $width; ?>px;height:<?php echo $height; ?>px'></div>
<script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $apiKey ?>"></script>
<script type="text/javascript">
  google.load("maps", "2.x");

  function bind(obj, fn)
  {
    return function() {
        return fn(obj);
    };
  }

  // Call this function when the page has been loaded
  function initialize() {
    var map = new google.maps.Map2(document.getElementById("map"));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    
    var center = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
    
    map.setCenter(center, <?php echo $zoom; ?>);
    
    <?php 
        if ($vars['pin']) { 
    ?>
            map.addOverlay(new GMarker(center));        
    <?php 
        } 
    ?>
    
    <?php
        if ($vars['nearby']) {
            foreach($vars['nearby'] as $org) {
    ?>        
                    var icon = new GIcon(G_DEFAULT_ICON);
                    icon.image = "<?php echo $org->getIcon('tiny'); ?>"
                    icon.iconSize = new GSize(20,20);
                    icon.iconAnchor = new GPoint(10, 10);
                    var markerOptions = { icon:icon };
    
                var point = new GLatLng(<?php echo $org->getLatitude(); ?>, <?php echo $org->getLongitude(); ?>);
                var marker = new GMarker(point, markerOptions);
                
                GEvent.addListener(marker, 'click', bind(marker,
                    function (marker){
                        marker.openInfoWindowHtml([
                            '<h3><a href="<?php echo $org->getUrl(); ?>">',
                            <?php echo json_encode($org->name); ?>,
                            '</a></h3><p>',
                            <?php 
                                $description = $org->description;
                            
                                if ($description && strlen($description) > 300)
                                {
                                    $description = substr($description, 0, 300) ."...";
                                }    
                                echo json_encode($description); 
                                
                            ?>,
                            '</p>'
                        ].join(""), {maxWidth:350});
                    }
                ));
                
                map.addOverlay(marker);
    <?php        
            }
        }    
    ?>
    
  }
  google.setOnLoadCallback(initialize);
</script>

<?php
    } // not static
    else
    {
        echo "<div><img width='$width' height='$height' src='http://maps.google.com/maps/api/staticmap?center=$lat,$long&zoom=$zoom&size={$width}x$height&maptype=roadmap&markers=$lat,$long&sensor=false&key=$apiKey' /></div>";
    }
    
?>