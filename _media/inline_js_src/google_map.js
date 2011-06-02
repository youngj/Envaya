
/*
 * Loads markers for organizations on a google map. 
 * Nearby organizations are grouped into buckets
 */
MapLoader = function(fetchURLFn)
{
    this.bucketSize = 20; // pixel width/height for each bucket   
    this._fetchURLFn = fetchURLFn;
    
    this.displayedBuckets = {};
    this.lastFetchedBounds = null;
    this.fetchOrgXHR = null;
    
    this.setMap = function(map)
    {
        // initialize overlay classes which depend on google maps API being loaded alrady
        if (!MapLoader._initialized)
        {
            MapLoader._init();
            MapLoader._initialized = true;
        }
    
        this.map = map;        
        
        var infoOverlay = this.infoOverlay = new InfoOverlay();
        var loadingOverlay = this.loadingOverlay = new LoadingOverlay();
        
        var $self = this;
           
        infoOverlay.setMap(map);
        loadingOverlay.setMap(map);
        
        google.maps.event.addListener(map, 'click',  function() {
            infoOverlay.setBucket(null);
        });

        google.maps.event.addListener(map, 'bounds_changed', function () {
            infoOverlay.setBucket(null);
            loadingOverlay.draw();
            $self.load();
        });
    };


    this.reset = function()
    {
        this.lastFetchedBounds = null;
            
        for (var $bucketKey in this.displayedBuckets)
        {
            this.displayedBuckets[$bucketKey].setMap(null);
        }

        this.displayedBuckets = {};    
    };
    
    this.load = function()
    {    
        var map = this.map;
        var $bounds = map.getBounds();

        var $sw = $bounds.getSouthWest();
        var $ne = $bounds.getNorthEast();

        // avoid loading orgs if map was dragged very small amount
        if (this.lastFetchedBounds)
        {
            var proj = this.loadingOverlay.getProjection();
            var $oldSwPx = proj.fromLatLngToDivPixel(this.lastFetchedBounds.sw);
            var $oldNePx = proj.fromLatLngToDivPixel(this.lastFetchedBounds.ne);
            var $swPx = proj.fromLatLngToDivPixel($sw);
            var $nePx = proj.fromLatLngToDivPixel($ne);

            if (Math.abs($swPx.x - $oldSwPx.x) < this.bucketSize &&
                Math.abs($swPx.y - $oldSwPx.y) < this.bucketSize &&
                Math.abs($nePx.x - $oldNePx.x) < this.bucketSize &&
                Math.abs($nePx.y - $oldNePx.y) < this.bucketSize)
            {
                return;
            }
        }

        this.lastFetchedBounds = {sw:$sw,ne:$ne};
        this.loadingOverlay.show();
                
        if (this.fetchOrgXHR)
        {
            this.fetchOrgXHR.abort();
            this.fetchOrgXHR = null;
        }
        
        var $self = this;
        this.fetchOrgXHR = fetchJson(this._fetchURLFn($bounds), function(data) { $self._loaded(data); });
    };
    
    this._loaded = function($orgs)
    {
        for (var $bucketKey in this.displayedBuckets)
        {
            this.displayedBuckets[$bucketKey].hide();
        }

        var map = this.map;
        var $bounds = map.getBounds();
        var $zoom = map.getZoom();        
        var $proj = this.loadingOverlay.getProjection();

        var $buckets = {};
        
        var bucketSize = this.bucketSize;

        for (var $i = 0; $i < $orgs.length; $i++)
        {
            var $org = $orgs[$i];
            var $latlng = new google.maps.LatLng($org.latitude, $org.longitude);

            var $pixel = $proj.fromLatLngToDivPixel($latlng);

            var $bucketPixel = new google.maps.Point(
                Math.floor($pixel.x / bucketSize) * bucketSize + bucketSize / 2,
                Math.floor($pixel.y / bucketSize) * bucketSize + bucketSize / 2
            );

            var $bucketKey = $bucketPixel.x + "," + $bucketPixel.y + "," + $zoom;

            if (!$buckets[$bucketKey])
            {
                $buckets[$bucketKey] = new OrgBucket(
                    $proj.fromDivPixelToLatLng($bucketPixel)
                );            
            }
            $buckets[$bucketKey].addOrg($org);
        }

        for (var $bucketKey in $buckets)
        {
            var $bucket = $buckets[$bucketKey];

            if (!this.displayedBuckets[$bucketKey])
            {            
                $bucket.initialize(map, this.infoOverlay);
                this.displayedBuckets[$bucketKey] = $bucket;
            }
            this.displayedBuckets[$bucketKey].show();
        }
        
        this.loadingOverlay.hide();
    };
};

MapLoader._init = function() {

DivOverlay = function(div)
{
    this._div = div;
    this._pane = 'overlayImage';

    this.onAdd = function()
    {
        this.getPanes()[this._pane].appendChild(this._div);        
    };
      
    this.show = function()
    {
        this._div.style.display = 'block';
    };

    this.hide = function()
    {
        this._div.style.display = 'none';
    };

    this.onRemove = function()
    {
        removeElem(this._div);
    };
};

DivOverlay.prototype = new google.maps.OverlayView();

/*
 * An overlay which shows a "Loading..." message when fetching data from the server.
 */
LoadingOverlay = function()
{
    this._div = createElem('div', {id:'loadingOverlay'}, __('loading'));
    this._width = 0;
    this.hide();
        
    this.draw = function()
    {
        var container = this.getMap().getDiv();
        
        if (this._width == 0)
        {
            this._width = this._div.offsetWidth;
        }
        
        this._div.style.left = (container.offsetWidth - this._width - 10) + "px";
        this._div.style.top = "30px";    
    };
};

LoadingOverlay.prototype = new DivOverlay();

/*
 * An overlay which shows a list of organizations in the selected bucket,
 * linking to their home page.
 */
InfoOverlay = function()
{
    this._div = createElem('div', {id:'infoOverlay'});        
    
    this.hide();
    
    this.onAdd = function()
    {
        // add to document body, not map pane, because info overlay may extend beyond map pane
        var mapElem = this.getMap().getDiv();
        document.body.appendChild(this._div);
    };    
    
    this.setBucket = function(bucket)
    {
        this.bucket = bucket;
        
        if (this.bucket == null)
        {
            this.hide();
            return;
        }
        
        var orgs = bucket.orgs;
        var center = bucket.center;
    
        removeChildren(this._div);
        var div = createElem('div');
        this._div.appendChild(div);
        
        var $orgInnerDiv = createElem('div');

        if (orgs.length > 8)
        {
            div.appendChild(this._makeBucketControls());

            div.style.overflow = 'auto';
            div.style.height = '145px';
            div.style.width = '400px';

            $orgInnerDiv.style.overflow = 'hidden';
            $orgInnerDiv.style.textOverflow = 'ellipsis';
            $orgInnerDiv.style.whiteSpace = 'nowrap';
            $orgInnerDiv.style.width = '380px';
        }        
                
        div.appendChild($orgInnerDiv);

        for (var $i = 0; $i < orgs.length; $i++)
        {
            $orgInnerDiv.appendChild(this._makeOrgLink(orgs[$i]));
            $orgInnerDiv.appendChild(createElem('br'));
        }
        
        this.draw();
        this.show();
    };
    
    this._makeBucketControls = function()
    {
        var orgs = this.bucket.orgs;
        var center = this.bucket.center;
    
        var $div = createElem('div', {className:'mapBucketControls'});

        var map = this.getMap();
        
        var $curZoom = map.getZoom();        
        var $maxZoom = map.mapTypes[map.getMapTypeId()].maxZoom;
       
        if ($curZoom < $maxZoom && orgs.length > 1)
        {
            $div.appendChild(createElem('a', {
                href:'javascript:void(0)',
                click: function() {
                    map.setCenter(center);
                    map.setZoom(Math.min($curZoom + 3, $maxZoom));
                }
            }, __('map:zoom_in')));
        }
        return $div;
    };    
    
    this._makeOrgLink = function(org)
    {
        return createElem('a', {
            href: org.url, 
            className: 'mapOrgLink', 
            title: org.name
        }, org.name);
    };    
    
    this.draw = function()
    {
        if (this.bucket)
        {
            var mapElem = this.getMap().getDiv();
            var proj = this.getProjection();
            var point = proj.fromLatLngToDivPixel(this.bucket.center);
            this._div.style.left = (mapElem.offsetLeft + point.x + 13) + "px";
            this._div.style.top = (mapElem.offsetTop + point.y - 12) + "px";
        }
    };    
};

InfoOverlay.prototype = new DivOverlay();

/*
 * An overlay which acts as a marker at a given location, showing the number
 * of organizations in that bucket. Clicking the marker will show the InfoOverlay
 * listing organizations in the bucket.
 */
OrgBucket = function($center)
{
    this.center = $center;
    this.orgs = [];
};

(function() {

var $proto = function() {
    
    this.initialize = function(map, infoOverlay)
    {
        this.setMap(map);
        this._infoOverlay = infoOverlay;
     
        var $div = this._div = createElem('div', {
            className:'mapMarker'
        });

        var $self = this;        
        google.maps.event.addDomListener(this._div, 'click',  function(event) {
            $self._clicked();
            if (event.stopPropagation) { event.stopPropagation(); }
            event.cancelBubble = true;
        }, true);        
        
        var $img = createElem('img');

        if (this.orgs.length > 1)
        {
            $img.src = "/_graphics/placemark_n.gif";

            $div.appendChild(
                createElem('span', {className:'mapMarkerCount', innerHTML: "" + this.orgs.length})
            );
        }
        else
        {
            $img.src = "/_graphics/placemark.gif";
        }

        $div.appendChild($img);            
    }
    
    this.draw = function() {
        var proj = this.getProjection();
        var point = proj.fromLatLngToDivPixel(this.center);
        
        this._div.style.left = (point.x - 12) + "px";
        this._div.style.top = (point.y - 12) + "px";        
    };
    
    this._clicked = function()
    {
        if (this._infoOverlay.bucket != this)
        {
            this.orgs.sort(function(a,b) {

                a._lcName = a._lcName || a.name.toLowerCase();
                b._lcName = b._lcName || b.name.toLowerCase();

                if (a._lcName < b._lcName)
                {
                    return -1;
                }
                else if (a._lcName > b._lcName)
                {
                    return 1;
                }
                return 0;
            });

            this._infoOverlay.setBucket(this);
        }
    };

    this.addOrg = function($org)
    {
        this.orgs.push($org);
    };
    
};

$proto.prototype = new DivOverlay();
OrgBucket.prototype = new $proto();

})();

};