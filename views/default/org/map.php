
<?php

    $editPinMode = @$vars['edit'];
    $zoom = ((int)@$vars['zoom']) ?: 10;
    $width = @$vars['width'] ?: 460;
    $height = @$vars['height'] ?: 280;
    $mapType = @$vars['mapType'] ?: "G_NORMAL_MAP";
    $nearby = @$vars['nearby'] ?: false;

    global $CONFIG;
    $apiKey = $CONFIG->google_api_key;
    $lat = (float)$vars['lat'];
    $long = (float)$vars['long'];

    if (!@$vars['static'])
    {
        if ($editPinMode && !@$vars['pin'])
        {
?>
            <div id="dropPinBtn">
            <a href="javascript:dropPin();"><?php echo elgg_echo("map:drop_pin"); ?></a>
            </div>
<?php
        }
?>

<label id="pinDragInstr" style="display:none;">
<?php echo elgg_echo("map:drag_pin"); ?>
</label>

<div id='map' style='width:<?php echo $width; ?>px;height:<?php echo $height; ?>px'></div>
<div id='infoOverlay'></div>
<div id='loadingOverlay'><?php echo elgg_echo('loading') ?></div>

<script type='text/javascript'>

var sector = <?php echo (int)$vars['sector'] ?>;

function setMapSector($sector)
{
    sector = $sector;
    if (window.map)
    {
        fetchOrgs();
    }
}

</script>

<script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo $apiKey ?>"></script>
<script type="text/javascript">
google.load("maps", "2.x");

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
    <?php if ($editPinMode) { ?>

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

    <?php } else { ?>

    map.addOverlay(new GMarker($ll));

    <?php } ?>
}

var info = document.getElementById('info');

var displayedBuckets = {};

OrgBucket = function($center, $orgs)
{
    this.center = $center;
    this.orgs = $orgs || [];
};

var infoOverlay = document.getElementById('infoOverlay');
var loadingOverlay = document.getElementById('loadingOverlay');
var expandedBucket = null;

function closeExpandedBucket()
{
    infoOverlay.style.display = 'none';
    expandedBucket = null;
}

OrgBucket.prototype = new function() {
    this.initialize = function(map)
    {
        this._map = map;
        var $pane = map.getPane(G_MAP_MARKER_PANE);

        var $div = this._div = document.createElement('div');
        $div.className = 'mapMarker';

        var $img = document.createElement('img');

        var $self = this;
        addEvent($div, 'click', function() {
            $self._clicked();
        });

        if (this.orgs.length > 1)
        {
            $img.src = "/_graphics/placemark_n.gif";

            var $span = document.createElement('span');
            $span.className = "mapMarkerCount";
            $span.innerHTML = "" + this.orgs.length;
            $div.appendChild($span);
        }
        else
        {
            $img.src = "/_graphics/placemark.gif";
        }

        $div.appendChild($img);

        this._setPosition();

        $pane.appendChild($div);
    };

    this._makeOrgLink = function($org)
    {
        var $link = document.createElement('a');
        $link.href = $org.url;
        $link.className = 'mapOrgLink';
        $link.title = $org.name;

        $link.appendChild(document.createTextNode($org.name));
        return $link;
    };

    this._makeBucketControls = function()
    {

        var $div = document.createElement('div');
        $div.className = 'mapBucketControls';

        var $curZoom = this._map.getZoom();
        var $maxZoom = this._map.getCurrentMapType().getMaximumResolution();

        if ($curZoom < $maxZoom && this.orgs.length > 1)
        {
            var $a = document.createElement('a');
            $a.href = 'javascript:void(0)';
            var $self = this;

            addEvent($a, 'click', function() {
                $self._map.setCenter($self.center, Math.min($curZoom + 3, $maxZoom));
            });
            $a.appendChild(document.createTextNode(<?php echo json_encode(elgg_echo('zoom_in')) ?>));
            $div.appendChild($a);
        }

        return $div;
    };

    this._clicked = function()
    {
        if (expandedBucket != this)
        {
            expandedBucket = this;

            removeChildren(infoOverlay);

            this.orgs.sort(function(a,b) {
                if (a.name < b.name)
                {
                    return -1;
                }
                else if (a.name > b.name)
                {
                    return 1;
                }
                return 0;
            });


            var $orgDiv = document.createElement('div');
            var $orgInnerDiv = document.createElement('div');
            $orgDiv.appendChild($orgInnerDiv);

            for (var $i = 0; $i < this.orgs.length; $i++)
            {
                $orgInnerDiv.appendChild(this._makeOrgLink(this.orgs[$i]));
                $orgInnerDiv.appendChild(document.createElement('br'));
            }

            infoOverlay.appendChild($orgDiv);

            this._setInfoPosition();
            infoOverlay.style.display = 'block';

            if (this.orgs.length > 8)
            {
                infoOverlay.appendChild(this._makeBucketControls());

                $orgDiv.style.overflow = 'auto';
                $orgDiv.style.height = '145px';
                $orgDiv.style.width = '400px';

                $orgInnerDiv.style.overflow = 'hidden';
                $orgInnerDiv.style.textOverflow = 'ellipsis';
                $orgInnerDiv.style.whiteSpace = 'nowrap';

                $orgInnerDiv.style.width = '380px';
            }
        }
    };

    this._setInfoPosition = function()
    {
        var $point = this._map.fromLatLngToContainerPixel(this.center);

        var $mapElem = document.getElementById('map');

        infoOverlay.style.left = ($mapElem.offsetLeft + $point.x + 13) + "px";
        infoOverlay.style.top = ($mapElem.offsetTop + $point.y - 12) + "px";
    };

    this._setPosition = function()
    {
        var $point = this._map.fromLatLngToDivPixel(this.center);
        this._div.style.left = ($point.x - 12) + "px";
        this._div.style.top = ($point.y - 12) + "px";
    };

    this.addOrg = function($org)
    {
        this.orgs.push($org);
    }

    this.show = function()
    {
        this._div.style.display = 'block';
    };

    this.hide = function()
    {
        this._div.style.display = 'none';
    };

    this.remove = function()
    {
        this._div.parentNode.removeChild(this._div);
    };

    this.copy = function()
    {
        return new BucketMarker(this.center, this.orgs);
    };

    this.redraw = function($force)
    {
        if ($force)
        {
            this._setPosition();
        }

        if (expandedBucket == this)
        {
            this._setInfoPosition();
        }
    };

    this.getKml = function(callback) {

    };
};

function showOrgs($data)
{
    for (var $bucketKey in displayedBuckets)
    {
        if (displayedBuckets.hasOwnProperty($bucketKey))
        {
            displayedBuckets[$bucketKey].hide();
        }
    }

    var $orgs = $data;

    var $bounds = map.getBounds();
    var $proj = map.getCurrentMapType().getProjection();
    var $zoom = map.getZoom();
    var $bucketSize = 20; // pixels

    var $buckets = {};

    for (var $i = 0; $i < $orgs.length; $i++)
    {
        var $org = $orgs[$i];
        var $latlng = new GLatLng($org.latitude, $org.longitude);

        var $pixel = $proj.fromLatLngToPixel($latlng, $zoom);

        var $bucketPixel = new GPoint(
            Math.floor($pixel.x / $bucketSize) * $bucketSize + $bucketSize / 2,
            Math.floor($pixel.y / $bucketSize) * $bucketSize + $bucketSize / 2
        );

        var $bucketKey = $bucketPixel.x + "," + $bucketPixel.y + "," + $zoom + "," + sector;

        if (!$buckets[$bucketKey])
        {
            $buckets[$bucketKey] = new OrgBucket($proj.fromPixelToLatLng($bucketPixel, $zoom));
        }

        $buckets[$bucketKey].addOrg($org);
    }

    for (var $bucketKey in $buckets)
    {
        if ($buckets.hasOwnProperty($bucketKey))
        {
            var $bucket = $buckets[$bucketKey];

            var $displayedBucket = displayedBuckets[$bucketKey];
            if ($displayedBucket)
            {
                $displayedBucket.show();
            }
            else
            {
                map.addOverlay($bucket);
                displayedBuckets[$bucketKey] = $bucket;
            }
        }
    }

    loadingOverlay.style.display = 'none';
}

var nearbyOrgsCache = {};
var fetchOrgXHR = null;

function fetchOrgs()
{
    var $mapElem = document.getElementById('map');

    loadingOverlay.style.display = 'block';
    loadingOverlay.style.left = ($mapElem.offsetLeft + $mapElem.offsetWidth - loadingOverlay.offsetWidth - 10) + "px";
    loadingOverlay.style.top = ($mapElem.offsetTop + 30) + "px";

    var $bounds = map.getBounds();

    var $sw = $bounds.getSouthWest();
    var $ne = $bounds.getNorthEast();

    var $src = "/org/searchArea?latMin="+$sw.lat()+"&latMax="+$ne.lat()+"&longMin="+$sw.lng()+"&longMax="+$ne.lng()+"&sector=" + sector;
    if (window.displayUpdates)
    {
        $src = $src + "&updates=1";
    }

    if (fetchOrgXHR)
    {
        fetchOrgXHR.abort();
        fetchOrgXHR = null;
    }
    fetchOrgXHR = fetchJson($src, showOrgs);
}

var map = null;

function initialize()
{
    map = new google.maps.Map2(document.getElementById("map"));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    map.setMapType(<?php echo $mapType; ?>);

    var center = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);

    map.setCenter(center, <?php echo $zoom; ?>);

    <?php if (@$vars['pin']) { ?>

    placeMarker(center);

    <?php } ?>

    <?php if ($nearby) { ?>

    fetchOrgs();

    GEvent.addListener(map, 'click',  function(overlay) {
        closeExpandedBucket();
    });

    GEvent.addListener(map, 'moveend',
        function (marker)
        {
            closeExpandedBucket();
            fetchOrgs();
        }
    );

    <?php } ?>

}

google.setOnLoadCallback(initialize);

</script>

<?php if ($editPinMode) { ?>

<input type="hidden" id="orgLat" name="org_lat" value="" />
<input type="hidden" id="orgLng" name="org_lng" value="" />
<input type="hidden" id="mapZoom" name="map_zoom" value="" />

<?php } ?>

<?php
    } // not static
    else
    {
        echo "<div>";
        echo "<a href='org/browse/?lat=$lat&long=$long&zoom=10'>";

        echo "<img width='$width' height='$height' src='".get_static_map_url($lat, $long, $zoom, $width, $height)."' />";
        echo "</a>";
        echo "</div>";
    }

?>