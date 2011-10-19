
/*
 * Loads markers for organizations on a google map. 
 * Nearby organizations are grouped into buckets.
 */
OrgMapLoader = function()
{
    this.bucketSize = 20; // pixel width/height for each bucket   
    
    this.baseURL = "/pg/search_area";  
    this.idsURL = '/pg/js_orgs';
    this.displayedBuckets = {};
    this.lastFetchedBounds = null;
    this.fetchOrgXHR = null;
    
    this.loadIds = function(ids, successFn, errorFn)
    {
        return fetchJson(this.idsURL + '?ids=' + ids.join(','), successFn, errorFn);
    };
    
    this.setMap = function(map)
    {
        // initialize overlay classes which depend on google maps API being loaded alrady
        if (!OrgMapLoader._initialized)
        {
            OrgMapLoader._init();
            OrgMapLoader._initialized = true;
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
    
    // may be overridden to add additional parameters to the URL when fetching organizations
    this.getURLParams = function()
    {
        return {};
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
                   
        var urlParams = this.getURLParams();
        
        urlParams.lat_min = $sw.lat();
        urlParams.lat_max = $ne.lat();
        urlParams.long_min = $sw.lng();
        urlParams.long_max = $ne.lng();
        
        var div = map.getDiv();
        urlParams.width = div.offsetWidth;
        urlParams.height = div.offsetHeight;
        
        var paramsArr = [];        
        for (var name in urlParams)
        {
            paramsArr.push(name + "=" + encodeURIComponent(urlParams[name]));
        }
        
        var url = this.baseURL + (this.baseURL.indexOf('?') == -1 ? '?' : '&') + paramsArr.join('&');        
        
        this.fetchOrgXHR = fetchJson(url, function(data) { $self._loaded(data); });
    };
    
    this._loaded = function($bucketsData)
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

        // collect organizations in buckets of bucketSize x bucketSize pixels
        for (var $i = 0; $i < $bucketsData.length; $i++)
        {
            var $bucketData = $bucketsData[$i];
            var $latlng = new google.maps.LatLng($bucketData[0], $bucketData[1]);
            
            var $bucketKey = $bucketData[0] + "," + $bucketData[1] + "," + $zoom;

            $buckets[$bucketKey] = new OrgBucket(
                $latlng,
                $bucketData[2]
            );            
        }

        // reuse any existing OrgBucket markers when possible rather than initializing new ones
        for (var $bucketKey in $buckets)
        {
            var $bucket = $buckets[$bucketKey];

            if (!this.displayedBuckets[$bucketKey])
            {            
                $bucket.initialize(this);
                this.displayedBuckets[$bucketKey] = $bucket;
            }
            this.displayedBuckets[$bucketKey].show();
        }
        
        this.loadingOverlay.hide();
    };
};

OrgMapLoader._init = function() {

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
    
    this.onAdd = function()
    {
        // add to document body so that it doesn't move as the map is scrolled
        document.body.appendChild(this._div);
    };    
                
    this.draw = function()
    {
        var mapElem = this.getMap().getDiv();
        
        if (this._width == 0)
        {
            this._width = this._div.offsetWidth;
        }
        
        this._div.style.left = (mapElem.offsetLeft + mapElem.offsetWidth - this._width - 5) + "px";
        this._div.style.top = (mapElem.offsetTop + 5) + "px";
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
        document.body.appendChild(this._div);
    };    
    
    this.setBucket = function(bucket)
    {
        this.bucket = bucket;
        
        if (this.bucket == null || this.bucket.orgs == null)
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
            }, __('browse:zoom_in')));
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
            var point = proj.fromLatLngToContainerPixel(this.bucket.center);
            
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
OrgBucket = function($center, $ids)
{
    this.center = $center;
    this.count = $ids.length;
    this.ids = $ids;
    this.orgs = null;
};

(function() {

var $proto = function() {
    
    this.initialize = function(loader)
    {
        this.setMap(loader.map);
        
        this._loader = loader;
     
        var $div = this._div = createElem('div', {
            className:'mapMarker'
        });

        var $self = this;        
        google.maps.event.addDomListener(this._div, 'click',  function(event) {
            $self._clicked();
            if (event.stopPropagation) { event.stopPropagation(); }
            event.cancelBubble = true;
        }, true);        
        
        // preload bucket on mouseover
        google.maps.event.addDomListener(this._div, 'mouseover',  function(event) {
            $self._loadOrgs();
        }, true);                
        
        var $img = createElem('img');

        if (this.count > 1)
        {
            $img.src = "/_media/images/placemark_n.gif";

            var countSpan = createElem('span', {className:'mapMarkerCount', innerHTML: "" + this.count});
            
            $div.appendChild(countSpan);
            
            if (this.count >= 100)
            {
                countSpan.style.fontSize = '9px';
                countSpan.style.top = "5px";
            }
        }
        else
        {
            $img.src = "/_media/images/placemark.gif";
        }

        $div.appendChild($img);            
    }
    
    this.draw = function() {
        var proj = this.getProjection();
        var point = proj.fromLatLngToDivPixel(this.center);
        
        this._div.style.left = (point.x - 12) + "px";
        this._div.style.top = (point.y - 12) + "px";        
    };
    
    this._isSelected = function()
    {
        return this._loader.infoOverlay.bucket == this;
    };
    
    this._loadOrgs = function()
    {
        if (this.orgs != null)
        {
            return;
        }
        
        if (this._isSelected())
        {
            this._loader.loadingOverlay.show();
        }
    
        if (this._xhr)
        {
            return;
        }
    
        var self = this;
        
        this._xhr = this._loader.loadIds(this.ids, function(orgs) {
            self._orgsLoaded(orgs);
        }, function(err) {  
            self._loadError(err);
        }); 
    };

    this._loadError = function(err)
    {
        this._loader.loadingOverlay.hide();
        this._xhr = null;
        alert(err.error);
    }
    
    this._orgsLoaded = function(orgs)
    {        
        this._xhr = null;
        this.orgs = orgs;
        
        this._loader.loadingOverlay.hide();
        
        if (this._isSelected())
        {   
            this._loader.infoOverlay.setBucket(this);
        }
    };
    
    this._clicked = function()
    {
        if (this._loader.infoOverlay.bucket != this)
        {
            this._loader.infoOverlay.setBucket(this);
            this._loadOrgs();
        }
    };    
};

$proto.prototype = new DivOverlay();
OrgBucket.prototype = new $proto();

})();

};