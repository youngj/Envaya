
<?php

    $editPinMode = $vars['edit'];
    $zoom = ((int)$vars['zoom']) ?: 10;
    $width = $vars['width'] ?: 460;
    $height = $vars['height'] ?: 280;    
    $mapType = $vars['mapType'] ?: "G_NORMAL_MAP";
    $nearby = $vars['nearby'] ?: false;
    
    global $CONFIG;
    $apiKey = $CONFIG->google_api_key;
    $lat = (float)$vars['lat'];
    $long = (float)$vars['long'];

    if (!$vars['static'])
    {
        if ($editPinMode && !$vars['pin'])
        {
?>          
            <div id="dropPinBtn">
            <a href="javascript:dropPin();"><?php echo elgg_echo("org:mapDropPin"); ?></a>
            </div>
<?php
        }
?>

<div id="pinDragInstr" style="display:none;">
<?php echo elgg_echo("org:mapPinDragInstr"); ?>
</div>

<div id='map' style='width:<?php echo $width; ?>px;height:<?php echo $height; ?>px'></div>
<div id='mapOverlay' style='position:absolute;padding:5px;background:white;left:0px;top:0px;display:none'></div>
<script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $apiKey ?>"></script>
<script type="text/javascript">
  google.load("maps", "2.x");

  function bind(obj, fn)
  {
    return function() {
        return fn(obj);
    };
  }

    function removeChildren($elem)
    {
        while ($elem.firstChild)
        {
            $elem.removeChild($elem.firstChild);
        }
    }

  function dropPin()
  {
    var $ll = map.getCenter();
    if($ll)
    {
        placeMarker($ll);
        document.getElementById("dropPinBtn").style.display = "none";
    }
  }
  
  function setSavedLL($ll)
  {
      document.getElementById("orgLat").value = $ll.lat();
      document.getElementById("orgLng").value = $ll.lng();
      setSavedMapState();
  }
  
  function setSavedMapState()
  {
      document.getElementById("mapZoom").value = map.getZoom();
  }
  
  function placeMarker($ll)
  {
      <?php
      if ($editPinMode) {
      ?>
          var marker = new GMarker($ll, {draggable: true});

          GEvent.addListener(marker, "dragend", function(latlng) {
                setSavedLL(latlng);
                map.setCenter(latlng);
            });
            
          GEvent.addListener(map, "zoomend", function() {
                setSavedMapState();
            });            
      
          map.addOverlay(marker);
          setSavedLL($ll);
          document.getElementById("pinDragInstr").style.display = "block";
      <?php
      }
      else {
      ?>
        map.addOverlay(new GMarker($ll));
      <?php
      }
      ?>
  }  
  
    var $displayedOrgs = {};
  
    function showIcon($org)
    {    
        if ($displayedOrgs[$org.guid])        
        {
            return;
        }
        $displayedOrgs[$org.guid] = 1;
    
        var icon = new GIcon(G_DEFAULT_ICON);
        icon.image = $org.icon;
        icon.iconSize = new GSize(20,20);
        icon.iconAnchor = new GPoint(10, 10);
        var markerOptions = { icon:icon };

        var point = new GLatLng($org.latitude, $org.longitude);
        var marker = new GMarker(point, markerOptions);

        GEvent.addListener(marker, 'mouseover', bind(marker,
            function (marker)
            {                            
                var latLng = marker.getLatLng();
                var pixel = map.fromLatLngToDivPixel(latLng);
                                
                var mapOverlay = document.getElementById('mapOverlay');
                removeChildren(mapOverlay);
                mapOverlay.appendChild(document.createTextNode($org.name));           
                
                var mapElem = document.getElementById('map');
                mapOverlay.style.left = (mapElem.offsetLeft + pixel.x + 10) + "px";
                mapOverlay.style.top = (mapElem.offsetTop + pixel.y + 10) + "px";
                mapOverlay.style.display = 'block';
            }
        ));
        
        GEvent.addListener(marker, 'mouseout', bind(marker,
            function (marker)
            {
                var mapOverlay = document.getElementById('mapOverlay');
                removeChildren(mapOverlay);
                mapOverlay.style.display = 'none';
            }
        ));        
        
        GEvent.addListener(marker, 'click', bind(marker,
            function (marker)
            {
                window.location.href = $org.url;
            }        
        ));        

        map.addOverlay(marker);
    }    

    function searchAreaCallback($data)
    {
        for (var $i = 0; $i < $data.length; $i++)
        {
            showIcon($data[$i]);
        }
    }

    function fetchOrgs()
    {
        var $script = document.createElement('script');

        var $bounds = map.getBounds();

        var $sw = $bounds.getSouthWest();
        var $ne = $bounds.getNorthEast();

        $script.src = "org/searchArea?latMin="+$sw.lat()+"&latMax="+$ne.lat()+"&longMin="+$sw.lng()+"&longMax="+$ne.lng()+"&sector=<?php echo (int)$vars['sector'] ?>";            
        $script.charset = 'utf-8';
        document.body.appendChild($script);
    }    
  
  
  // Call this function when the page has been loaded
  function initialize() {
    map = new google.maps.Map2(document.getElementById("map"));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    map.setMapType(<?php echo $mapType; ?>);
    
    var center = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);

    map.setCenter(center, <?php echo $zoom; ?>);
        
    <?php
        if ($vars['pin']) {
    ?>
            placeMarker(center);
    <?php
        }
    ?>

    <?php
        if ($nearby) {
    ?>       
        fetchOrgs(); 
        
        GEvent.addListener(map, 'moveend', 
            function (marker)
            {                            
                fetchOrgs();
            }
        );        
        
    <?php    
        }
    ?>

  }
  google.setOnLoadCallback(initialize);
</script>

<?php 
    if ($editPinMode) {
?>
    <input type="hidden" id="orgLat" name="org_lat" value="" />
    <input type="hidden" id="orgLng" name="org_lng" value="" />
    <input type="hidden" id="mapZoom" name="map_zoom" value="" />
<?php
}
?>

<?php
    } // not static
    else
    {
        echo "<div>";
		echo "<a href='org/browse/?lat=$lat&long=$long&zoom=10'>";
        echo "<img width='$width' height='$height' src='http://maps.google.com/maps/api/staticmap?center=$lat,$long&zoom=$zoom&size={$width}x$height&maptype=roadmap&markers=$lat,$long&sensor=false&key=$apiKey' />";
		echo "</a>";
        echo "</div>";
    }

?>