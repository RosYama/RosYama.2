/**
 * @name YandexClusterer for Yandex Maps.
 * @version 1.0.0 [August 13, 2011]
 * @author Alexander Shabunevich [http://aether.ru]
 * http://beholder.bitbucket.org/yandex.clusterer/
 *
 * Based on MarkerClustererPlus for Google Maps V3 by Gary Little
 */

/**
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Clusterer main class.
 * Init only once for each map.
 *
 * @constructor
 * @param {YMaps.Map} map Yandex map object
 * @param {Array.<YMaps.Placemark>} [markers] Array of placemarks to add
 * @param {Object} [opts] Options object
 */
function YandexClusterer(map, markers, opts) {
  this.map = map;
  this.bounds = null;
  this.clusters = [];
  this.markers = markers || [];
  this.zoom = null;
  this.map.YandexClusterer = this;

  this.om = new YMaps.ObjectManager(); // cluster's object manager
  this.map.addOverlay(this.om);

  // options
  opts = opts || {};
  this.max_zoom = opts.max_zoom || 0;
  this.grid = opts.grid || 60;
  this.min_size = opts.min_size || 2;
  this.centered = opts.centered || false;
  this.batch = opts.batch || 400;
  this.style = opts.style || {
    icon: 'http://gmaps-utility-library.googlecode.com/svn/trunk/markerclusterer/images/m1.png',
    height: 52,
    width: 53,
    offset: [-152, -153],
    textColor: '#000000',
    textSize: 11,
    printable: false
  };

  // check if there are previous events - remove them
  if (this.map._yandexClustererEvents) {
    for (event in this.map._yandexClustererEvents) {
      this.map._yandexClustererEvents[event].cleanup();
    }
  }
  // add new events
  this.map._yandexClustererEvents = {};
  this.map._yandexClustererEvents['update'] = YMaps.Events.observe(this.map, this.map.Events.Update, function () {
    this.repaint();
  }, this);
  this.map._yandexClustererEvents['move'] = YMaps.Events.observe(this.map, this.map.Events.MoveEnd, function () {
    this.redraw();
  }, this);

  this.repaint();
}

/**
 * Add markers to clusterer.
 */
YandexClusterer.prototype.setMarkers = function(markers) {
  this.markers = markers;
}

/**
 * Remove all markers from map (clusters and single placemarks).
 */
YandexClusterer.prototype.clearMarkers = function() {
  var l = this.markers.length+1;
  for (var j in this.markers){
  	var marker = this.markers[j];
    marker.isAdded = false;
    if (marker.isOnMap) {
      marker.isOnMap = false;
    }
  }
  this.om.removeAll();

  l = this.clusters.length;
  while (l--) {
    this.clusters[l].remove();
  }
  this.clusters = [];
}

/**
 * Removes all markers and recalculate clusters.
 * Used after zoom changed.
 */
YandexClusterer.prototype.repaint = function() {
  this.clearMarkers();
  this.redraw();
}

/**
 * Recalculate clusters. Markers not removed.
 * Used after map moved.
 */
YandexClusterer.prototype.redraw = function() {
  this.createClusters(0);
}

/**
 * Create clusters for markers. Working in several iterations.
 */
YandexClusterer.prototype.createClusters = function(iter) {
  var self = this,
      i = iter,
      length = this.markers.length,
      iter_last = Math.min(iter + this.batch, length);

  // if first iteration
  if (iter === 0) {
    this.bounds = this.extendBounds(this.map.getBounds());
    this.zoom = this.map.getZoom();
    if (typeof this.timerRefStatic !== "undefined") {
      clearTimeout(this.timerRefStatic);
      delete this.timerRefStatic;
    }
  }

  for (var i in this.markers){
    var marker = this.markers[i];
    if (!marker.isAdded && this.bounds.contains(marker.getCoordPoint())) {
      this.addToCluster(marker);
    }
  }

  if (iter_last < length) {
    this.timerRefStatic = setTimeout(function () {
      self.createClusters(iter_last);
    }, 0);
  } else {
    delete this.timerRefStatic;
    setTimeout(function () {
      self.updateClusterIcons();
    }, 0);
  }
}

/**
 * Extends map bounds for grid size.
 */
YandexClusterer.prototype.extendBounds = function(bounds) {
  var converter = this.map.converter,
      rtPix, lbPix, ne, sw;

  // Convert the points to pixels and the extend out by the grid size.
  rtPix = converter.coordinatesToMapPixels(bounds.getRightTop());
  rtPix.x += this.grid;
  rtPix.y -= this.grid;

  lbPix = converter.coordinatesToMapPixels(bounds.getLeftBottom());
  lbPix.x -= this.grid;
  lbPix.y += this.grid;

  // Convert the pixel points back to LatLng
  ne = converter.mapPixelsToCoordinates(rtPix);
  sw = converter.mapPixelsToCoordinates(lbPix);

  return new YMaps.GeoBounds(sw, ne);
}

/**
 * Add marker to cluster (new or closest).
 * See also Cluster.addMarker().
 */
YandexClusterer.prototype.addToCluster = function(marker) {
  var d, cluster, center, l = this.clusters.length;
  var distance = 40000; // Some large number
  var clusterToAddTo = null;
  while (l--) {
    cluster = this.clusters[l];
    if (cluster.center) {
      d = this.distanceBetween(cluster.center, marker.getCoordPoint());
      if (d < distance) {
        distance = d;
        clusterToAddTo = cluster;
      }
    }
  }

  if (clusterToAddTo && clusterToAddTo.isMarkerInBounds(marker)) {
    clusterToAddTo.addMarker(marker);
  } else {
    cluster = new YandexCluster(this);
    cluster.addMarker(marker);
    this.clusters.push(cluster);
  }
}

/**
 * Calculates distance between points.
 */
YandexClusterer.prototype.distanceBetween = function (p1, p2) {
  var R = 6371; // Radius of the Earth in km
  var dLat = (p2.getLat() - p1.getLat()) * Math.PI / 180;
  var dLon = (p2.getLng() - p1.getLng()) * Math.PI / 180;
  var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(p1.getLat() * Math.PI / 180) * Math.cos(p2.getLat() * Math.PI / 180) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var d = R * c;
  return d;
};

/**
 * Updates all icons on map: clusters and single placemarks.
 */
YandexClusterer.prototype.updateClusterIcons = function() {
  var self = this,
      l = this.clusters.length,
      max_zoom = this.getMaxZoom(),
      max_map_zoom = this.map.getZoom();

  while (l--) {
    var cluster = this.clusters[l];
    count = cluster.markers.length;

    // Zoom too big
    if (max_map_zoom >= max_zoom) {
      var i = cluster.markers.length;
      while (i--) {
        var marker = cluster.markers[i];
        if (!marker.isOnMap) {
          this.om.add(marker, this.zoom, this.zoom);
          marker.isOnMap = true;
        }
      }
      continue;
    }
    // Min cluster size not yet reached.
    else if (count < this.min_size) {
      var i = cluster.markers.length;
      while (i--) {
        var marker = cluster.markers[i];
        if (!marker.isOnMap) {
          this.om.add(marker, this.zoom, this.zoom);
          marker.isOnMap = true;
        }
      }
      continue;
    }
    // show cluster icon, single placemarks are hidden
    else {
      var i = cluster.markers.length;
      while (i--) {
        var marker = cluster.markers[i];
        this.map.removeOverlay(marker);
        marker.isOnMap = false;
      }
    }

    cluster.showIcon(); // draw cluster icon
  }
}

/**
 * Returns max zoom for map.
 */
YandexClusterer.prototype.getMaxZoom = function () {
  return this.max_zoom || this.map.getMaxZoom();
};

/**
 * Cluster class.
 * Used internally from main class.
 *
 * @param {YandexClusterer} Associated Yandex clusterer class.
 */
function YandexCluster(mc) {
  this.mc = mc;
  this.om = mc.om;
  this.map = mc.map;
  this.center = null;
  this.markers = [];
  this.bounds = null;
  this.min_size = mc.min_size;
  this.style = mc.style;
  this.point = null;
}

/**
 * Add marker to this cluster.
 */
YandexCluster.prototype.addMarker = function(marker) {
  var count, i, zoom = this.map.getZoom();

  if (marker.isAdded) return false;

  if (!this.center) {
    this.center = marker.getCoordPoint();
    this.bounds = this.calculateBounds();
  } else if (this.mc.centered) {
    var l = this.markers.length + 1,
        m_coord = marker.getCoordPoint();
    var lat = (this.center.getLat() * (l - 1) + m_coord.getLat()) / l;
    var lng = (this.center.getLng() * (l - 1) + m_coord.getLng()) / l;
    this.center = new YMaps.GeoPoint(lng, lat);
    this.bounds = this.calculateBounds();
  }

  marker.isAdded = true;
  this.markers.push(marker);

  return true;
}

/**
 * Returns bounds for this cluster.
 */
YandexCluster.prototype.calculateBounds = function() {
  var bounds = new YMaps.GeoCollectionBounds(this.center);
  return this.mc.extendBounds(bounds);
};

/**
 * Check if marker is in bounds of cluster.
 */
YandexCluster.prototype.isMarkerInBounds = function(marker) {
  return this.bounds.contains(marker.getCoordPoint());
}

/**
 * Draw icon for cluster.
 */
YandexCluster.prototype.showIcon = function() {
  var self = this,
      position = new YMaps.GeoPoint(this.center.getLng(), this.center.getLat());

  // remove icon if it's already on map
  if (this.point) {
    this.om.remove(this.point);
  }

  // for printable version add <img> tag
  if (this.style.printable) {
    var template = new YMaps.Template('<div style="cursor:pointer;position:relative;line-height:$[style.iconStyle.size.y]px;height:$[style.iconStyle.size.y]px;width:$[style.iconStyle.size.x]px;text-align:center"><img src="$[style.iconStyle.href]" alt="" style="position:absolute;left:0;top:0"><span style="position:relative;font-weight:bold;font-size:$[textSize|12]px;color:$[textColor|#000]">$[markersCount|0]</span></div>');
  } else {
    var template = new YMaps.Template('<div style="cursor:pointer;position:relative;line-height:$[style.iconStyle.size.y]px;height:$[style.iconStyle.size.y]px;width:$[style.iconStyle.size.x]px;text-align:center;background:url($[style.iconStyle.href]) no-repeat;font-weight:bold;font-size:$[textSize|12]px;color:$[textColor|#000]">$[markersCount|0]</div>');
  }
  var style = new YMaps.Style();
  style.iconStyle = new YMaps.IconStyle(template);
  style.iconStyle.href = this.style.icon;
  style.iconStyle.size = new YMaps.Point(this.style.width, this.style.height);
  style.iconStyle.offset = new YMaps.Point(-26, -27);
  this.point = new YMaps.Placemark(position, {
    style: style,
    hasBalloon: false
  });
  this.point.markersCount = this.markers.length;
  this.point.textSize = this.style.textSize;
  this.point.textColor = this.style.textColor;

  // click event - zoom to cluster bounds
  YMaps.Events.observe(this.point, this.point.Events.Click, function () {
    this.map.setBounds(this.bounds);
    // Don't zoom beyond the max zoom level
    if (this.mc.getMaxZoom() < this.map.getZoom()) {
      this.map.setZoom(this.mc.getMaxZoom() + 1);
    }
  }, this);

  var zoom = this.map.getZoom();
  this.om.add(this.point, zoom, zoom);
}

/**
 * Remove cluster from map.
 */
YandexCluster.prototype.remove = function () {
  this.markers = [];
  if (this.point) {
    this.point = null;
  }
};

var JCBXYandexSearch_arSerachresults;

if (!window.BX_YMapAddPlacemark)
{
	window.BX_YMapAddPlacemark = function(map, arPlacemark)
	{
		if (null == map)
			return false;
		
		if(!arPlacemark.LAT || !arPlacemark.LON)
			return false;
		
		var s = new YMaps.Style();
		s.iconStyle = new YMaps.IconStyle();
		s.iconStyle.href = "/images/st1234/"+arPlacemark.TYPE+"_"+arPlacemark.STATE+".png";
		s.iconStyle.size = new YMaps.Point(54, 61);
		s.iconStyle.offset = new YMaps.Point(-30, -61);
		
		var obPlacemark = new map.bx_context.YMaps.Placemark(new map.bx_context.YMaps.GeoPoint(arPlacemark.LON, arPlacemark.LAT),{hasHint: true, hideIcon: false, hasBalloon: false, style: s });
		
		if (null != arPlacemark.TEXT && arPlacemark.TEXT.length > 0)
		{
			obPlacemark.setBalloonContent(arPlacemark.TEXT.replace(/\n/g, '<br />'));
			var value_view = '';
			if (arPlacemark.TEXT.length > 0)
			{
				var rnpos = arPlacemark.TEXT.indexOf("\n");
				value_view = rnpos <= 0 ? arPlacemark.TEXT : arPlacemark.TEXT.substring(0, rnpos);
			}
			obPlacemark.setIconContent(value_view);
		}
		YMaps.Events.observe(obPlacemark, obPlacemark.Events.Click, function (obj)
			{
				window.location="/"+arPlacemark.ID+"/";
			}
		);
		map.addOverlay(obPlacemark);
		return obPlacemark;
	}
}

if (!window.BX_YMapAddPolyline)
{
	window.BX_YMapAddPolyline = function(map, arPolyline)
	{
		if (null == map)
			return false;
		
		if (null != arPolyline.POINTS && arPolyline.POINTS.length > 1)
		{
			var arPoints = [];
			for (var i = 0, len = arPolyline.POINTS.length; i < len; i++)
			{
				arPoints[i] = new map.bx_context.YMaps.GeoPoint(arPolyline.POINTS[i].LON, arPolyline.POINTS[i].LAT);
			}
		}
		else
		{
			return false;
		}
		
		if (null != arPolyline.STYLE)
		{
			var obStyle = new map.bx_context.YMaps.Style();
			obStyle.lineStyle = new map.bx_context.YMaps.LineStyle();
			obStyle.lineStyle.strokeColor = arPolyline.STYLE.lineStyle.strokeColor;
			obStyle.lineStyle.strokeWidth = arPolyline.STYLE.lineStyle.strokeWidth;
			var style_id = "bitrix#line_" + Math.random();
			map.bx_context.YMaps.Styles.add(style_id, obStyle);
		}
		
		var obPolyline = new map.bx_context.YMaps.Polyline(
			arPoints,
			{style: style_id, clickable: true}
		);
		obPolyline.setBalloonContent(arPolyline.TITLE);
		map.addOverlay(obPolyline);
		return obPolyline;
	}
}


var JCBXYandexSearch = function(map_id, obOut, jsMess)
{
	var _this = this;
	this.map_id = map_id;
	this.map = GLOBAL_arMapObjects[this.map_id];
	this.obOut = obOut;
	if (null == this.map)
		return false;
	this.arSearchResults = [];
	this.jsMess = jsMess;
	this.__searchResultsLoad = function(geocoder)
	{
		if (null == _this.obOut)
			return;
		_this.obOut.innerHTML = '';
		_this.clearSearchResults();
		var obList = null;
		if (len = geocoder.length()) 
		{
			obList = document.createElement('UL');
			obList.className = 'bx-yandex-search-results';
			var str = '';
			str += _this.jsMess.mess_search + ': <b>' + len + '</b> ' + _this.jsMess.mess_found + '.';
			for (var i = 0; i < len; i++)
			{
				_this.arSearchResults[i] = geocoder.get(i);
				_this.map.addOverlay(_this.arSearchResults[i]);
				YMaps.Events.observe
				(
					_this.arSearchResults[i],
					_this.arSearchResults[i].Events.Click,
					function(pm, ev)
					{
						GLOBAL_arMapObjects[map_id].setCenter(pm.getGeoPoint(), (pm.precision == 'other' || pm.precision == 'suggest' ? 10 : 15));
						clearSerchResults(map_id, _this.arSearchResults);
					}
				);
				var obListElement = document.createElement('LI');
				var obLink = document.createElement('A');
				obLink.href = "javascript:void(0)";
				obLink.appendChild(document.createTextNode(_this.arSearchResults[i].text));
				obLink.BXSearchIndex = i;
				obLink.onclick = _this.__showandclearResults;
				obListElement.appendChild(obLink);
				obList.appendChild(obListElement);
			}
			JCBXYandexSearch_arSerachresults = _this.arSearchResults;
			document.getElementById('clear_result_link').style.display = 'inline';
		} 
		else 
		{
			var str = _this.jsMess.mess_search_empty;
		}
		_this.obOut.innerHTML = str;
		if (null != obList)
			_this.obOut.appendChild(obList);
			
		_this.map.redraw();
	};
	this.__showandclearResults = function(index)
	{
		if (null == index || index.constructor == window.Event);
			index = this.BXSearchIndex;
		
		if (null != index && null != _this.arSearchResults[index])
		{
			_this.arSearchResults[index].openBalloon();
			_this.map.setCenter(_this.arSearchResults[index].getGeoPoint(), (_this.arSearchResults[index].precision == 'other' || _this.arSearchResults[index].precision == 'suggest' ? 10 : 15));
			document.getElementById("MAPLAT").value = _this.arSearchResults[index].getGeoPoint();
			document.getElementById("MAPZOOM").value = _this.map.getZoom();
			_this.clearSearchResults();
		}
		var ob = document.getElementById('results_' + _this.map_id);
		if(!ob)
		{
			return;
		}
		ob.innerHTML = '';
		document.getElementById('clear_result_link').style.display = 'none';
	}
	this.__showSearchResult = function(index)
	{
		if (null == index || index.constructor == window.Event);
			index = this.BXSearchIndex;
		if (null != index && null != _this.arSearchResults[index])
		{
			_this.arSearchResults[index].openBalloon();
			_this.map.panTo(_this.arSearchResults[index].getGeoPoint());
			document.getElementById("MAPLAT").value = _this.arSearchResults[index].getGeoPoint();
			document.getElementById("MAPZOOM").value = _this.map.getZoom();
		}
	};
	this.searchByAddress = function(str)
	{
		str = str.replace(/^[\s\r\n]+/g, '').replace(/[\s\r\n]+$/g, '');
		if (str == '')
			return;
		
		geocoder = new _this.map.bx_context.YMaps.Geocoder(str);
		_this.map.bx_context.YMaps.Events.observe(
			geocoder, 
			geocoder.Events.Load, 
			_this.__searchResultsLoad
		);
		_this.map.bx_context.YMaps.Events.observe(
			geocoder, 
			geocoder.Events.Fault, 
			_this.handleError
		);
	}
}

clearSerchResults = function(map_id, search_results)
{
	document.getElementById('clear_result_link').style.display = 'none';
	var ob = document.getElementById('results_' + map_id);
	if(!ob)
	{
		return;
	}
	ob.innerHTML = '';
	if(search_results)
	{
		for(var i in search_results)
		{
			GLOBAL_arMapObjects[map_id].removeOverlay(search_results[i]);
		}
	}
	else
	{
		for(var i in JCBXYandexSearch_arSerachresults)
		{
			GLOBAL_arMapObjects[map_id].removeOverlay(JCBXYandexSearch_arSerachresults[i]);
			JCBXYandexSearch_arSerachresults = new Array();
		}
	}
}

JCBXYandexSearch.prototype.handleError = function(error)
{
	alert(this.jsMess.mess_error + ': ' + error.message);
}

JCBXYandexSearch.prototype.clearSearchResults = function()
{
	for (var i = 0; i < this.arSearchResults.length; i++)
	{
		this.arSearchResults[i].closeBalloon();
		this.map.removeOverlay(this.arSearchResults[i]);
		delete this.arSearchResults[i];
	}

	this.arSearchResults = [];
}

var PlaceMarks = new Array();
var bAjaxInProgress = false;
var filter_state = {};
var filter_type = {};
var msie = YMaps.jQuery.browser.msie;
var clusterer;

var opts = {
          centered: msie ? false : true, // if not IE use centered clusters
          grid: msie ? 70 : 50 // for IE grid is bigger
        };
      

if (!window.GLOBAL_arMapObjects)
	window.GLOBAL_arMapObjects = {};

function onMapUpdate(m)
{
	var obj = document.getElementById('MAPLAT');
	if(obj)
	{
		obj.value = m.getCenter();
	}
	obj = document.getElementById('MAPZOOM');
	if(obj)
	{
		obj.value = m.getZoom();
	}
}

function init_MAP_DzDvWLBsil(context, type) 
{
	if (!type) type="show";
	if (null == context)
		context = window;

	if (!context.YMaps)
		return;
	
	window.GLOBAL_arMapObjects['MAP_DzDvWLBsil'] = new context.YMaps.Map(context.document.getElementById("BX_YMAP_MAP_DzDvWLBsil"));
	var map = window.GLOBAL_arMapObjects['MAP_DzDvWLBsil'];
	
	map.bx_context = context;
	var zoom=10;
	if (YMaps.location) {
					center = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);						
					if (YMaps.location.zoom) {
						zoom = YMaps.location.zoom;
					}				
					
				}else {
					center = new YMaps.GeoPoint(37.61763381958, 55.75578689575);					
				}
	// Установка для карты ее центра и масштаба
	map.setCenter(center, zoom, context.YMaps.MapType.MAP);			
	
	
	context.YMaps.Events.observe(map, map.Events.Update, function() { onMapUpdate(map); } );
	context.YMaps.Events.observe(map, map.Events.MoveEnd, function() { onMapUpdate(map); } );
	
	jQuery('#map-form input').live('change',function() {
			PlaceMarks=new Array();
			GetPlacemarks(map);
		});
		
	jQuery('#reset_button').live('click',function() {
			jQuery('#map-form input').attr('checked', false);
			PlaceMarks=new Array();
			GetPlacemarks(map);
		});	
	
	jQuery('#map-form').live('submit',function() {
			PlaceMarks=new Array();
			GetPlacemarks(map);
			return false;
		});
	
	
			map.enableScrollZoom();
				map.enableDblClickZoom();
				map.enableDragging();
				map.disableHotKeys();
				map.disableRuler();
				map.addControl(new context.YMaps.ToolBar());
				map.addControl(new context.YMaps.Zoom());
				map.addControl(new context.YMaps.MiniMap());
				map.addControl(new context.YMaps.TypeControl());
				map.addControl(new context.YMaps.ScaleLine());
				if (window.BXWaitForMap_searchMAP_DzDvWLBsil)
		{
							window.BXWaitForMap_searchMAP_DzDvWLBsil(map);
					}
				if (window.BX_SetPlacemarks_MAP_DzDvWLBsil)
		{
							window.BX_SetPlacemarks_MAP_DzDvWLBsil(map);
					}
		var loc = new String(document.location);
	loc = loc.split('#');
	if(loc[1])
	{
		loc = loc[1].split(';');
		loc[0] = loc[0].split(':');
		loc[1] = loc[1].split(':');
		loc[0][1] = loc[0][1].split(',');
		map.setCenter(new context.YMaps.GeoPoint(loc[0][1][0], loc[0][1][1]), loc[1][1]);
	}
	clusterer = new YandexClusterer(map, [], opts);  
	if (type=="addhole" || type=="updatehole") {
	map.disableDblClickZoom();
	YMaps.Events.observe(map, map.Events.DblClick, setCoordValue);
	}
	if (type=="updatehole") {	
	setCoordValue(map);
	}
	
}
      

function SetMarker(map,id,type,lat,lng,state)
{
					var s = new YMaps.Style();
					s.iconStyle = new YMaps.IconStyle();
					s.iconStyle.href = "/images/st1234/"+type+"_"+state+".png";
					s.iconStyle.size = new YMaps.Point(28, 30);
					s.iconStyle.offset = new YMaps.Point(-14, -30);
					if (! (id in PlaceMarks)){
						PlaceMarks[id] = new YMaps.Placemark(new YMaps.GeoPoint(lat, lng), { hasHint: false, hideIcon: false, hasBalloon: false, style: s });
						map.addOverlay(PlaceMarks[id]);
						YMaps.Events.observe
						(
							PlaceMarks[id],
							PlaceMarks[id].Events.Click,
							function (obj)
							{
								window.location="/"+id+"/";
							}
						)
					}

}

function GetPlacemarks(map)
{

	if(!bAjaxInProgress)
	{
		bAjaxInProgress = true;
		var mapBounds = map.getBounds();
		var exclude_id='';
		if ($('#Exclude_id').val()) exclude_id='&exclude_id='+$('#Exclude_id').val();
		var addr='/holes/ajaxMap/?bottom='+mapBounds.getBottom()+'&left='+mapBounds.getLeft()+'&top='+mapBounds.getTop()+'&'+jQuery('#map-form').serialize()+'&right='+mapBounds.getRight()+exclude_id+'&jsoncallback=?';
		//alert(addr);
		jQuery.getJSON(addr, function(data) {
			bAjaxInProgress = false;				
			for (i=0;i<data.markers.length;i++){			
				SetMarker(map, data.markers[i].id, data.markers[i].type, data.markers[i].lat, data.markers[i].lng, data.markers[i].state);  
				//alert (data.markers[i].type);
			}
			clusterer.setMarkers(PlaceMarks); // add markers to clusterer
            clusterer.repaint(); // update clusterer on map
		});
		
	}
	
}

function BX_SetPlacemarks_MAP_DzDvWLBsil(map)
{
	var arObjects = {PLACEMARKS:[],POLYLINES:[]};


	YMaps.Events.observe(map, map.Events.MoveEnd, function() {
		var res = "{ 'center': '" + map.getCenter() + "', 'zoom': '" + map.getZoom() + "' }"
		document.cookie = "map_settings="+res
		res = "center:" + map.getCenter() + ";zoom:" + map.getZoom();
		var loc = new String(document.location);
		loc = loc.split('#');
		document.location = loc[0] + '#' + res;
		GetPlacemarks(map);
	} );
	YMaps.Events.observe(map, map.Events.Move, function() { GetPlacemarks(map);	} );
	YMaps.Events.observe(map, map.Events.Update, function() {
		var res = "{ 'center': '" + map.getCenter() + "', 'zoom': '" + map.getZoom() + "' }"
		document.cookie = "map_settings="+res
		res = "center:" + map.getCenter() + ";zoom:" + map.getZoom();
		var loc = new String(document.location);
		loc = loc.split('#');
		document.location = loc[0] + '#' + res;
		GetPlacemarks(map);
	} );
	GetPlacemarks(map);
}

function BXWaitForMap_searchMAP_DzDvWLBsil() 
{
	window.jsYandexSearch_MAP_DzDvWLBsil = new JCBXYandexSearch('MAP_DzDvWLBsil', document.getElementById('results_MAP_DzDvWLBsil'), {
		mess_error: 'Ошибка',
		mess_search: 'Результаты поиска',
		mess_found: 'результатов найдено',
		mess_search_empty: 'Ничего не найдено'
	});
}

var coordpoint;

function setCoordValue(map, ev)
{
	if(coordpoint)
	{
		map.removeOverlay(coordpoint);
		coordpoint = null;
	}
	if (ev){
		$('#Holes_LATITUDE').val(ev.getCoordPoint().getY());
		$('#Holes_LONGITUDE').val(ev.getCoordPoint().getX());
	}
	var lon = $('#Holes_LATITUDE').val();
	var lat = $('#Holes_LONGITUDE').val();
	coordpoint = new YMaps.Placemark(new YMaps.GeoPoint(lat, lon), { style: 'default#violetPoint', draggable: true, hasBalloon: false, hideIcon: false });
	YMaps.Events.observe(coordpoint, coordpoint.Events.DragEnd, function (obj) {
		$('#Holes_LATITUDE').val(obj.getCoordPoint().getY());
		$('#Holes_LONGITUDE').val(obj.getCoordPoint().getX());
		geocodeOnSetCoordValue();
	});
	map.addOverlay(coordpoint);	
	geocodeOnSetCoordValue();
}

function geocodeOnSetCoordValue()
{
	var geocoder = new YMaps.Geocoder(coordpoint.getGeoPoint());
	YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {
		if(this.length())
		{
			
			var geo_text = this.get(0).text.split(',');
			var subjectrf;
			var city;
			var otherstr;
			subjectrf = geo_text[1];
			do
			{
				// сразу отрежем название страны
				geo_text[0] = '';
				document.getElementById('Holes_ADDRESS').value = geo_text.join(',').substr(2);
				// города - субъекты РФ				
				if(geo_text[1] == ' Москва' || geo_text[1] == ' Санкт-Петербург')
				{
					city = geo_text[1];
					geo_text[1] = '';
					// города-спутники
					if
					(
						geo_text[2] == ' Зеленоград'
						|| geo_text[2].indexOf('поселок') != -1
						|| geo_text[2].indexOf('город') != -1
						|| geo_text[2].indexOf('деревня') != -1
						|| geo_text[2].indexOf('село') != -1
					)
					{
						city        = geo_text[2];
						geo_text[2] = '';
						otherstr    = geo_text.join(',');
						break;
					}
					otherstr = geo_text.join(',');
					break;
				}
				// неизвестно что
				if(!geo_text[2])
				{
					city = '';
					otherstr = geo_text.join(',');
					break;
				}				
				geo_text[1] = '';
				// район или город
				if(geo_text[2].indexOf('район') != -1)
				{
					geo_text[2] = '';
					// точка попала в город
					if(geo_text[3])
					{
						city = geo_text[3];
						geo_text[3] = '';
						otherstr = geo_text.join(',');
					}
					// точка попала фиг знает куда
					else
					{
						city = '';
						otherstr = geo_text.join(',');
						break;
					}
				}
				else
				{
					city = geo_text[2];
					geo_text[2] = '';
					otherstr = geo_text.join(',');
				}
			}
			while(false);
			
		}
		else
		{
			city = otherstr = '';
		}
		while(otherstr.indexOf(',,') != -1)
		{
			otherstr = otherstr.replace(',,', '');
		}
		while(otherstr[0] == ' ' || otherstr[0] == ',')
		{
			otherstr = otherstr.substring(1);
		}  		
		document.getElementById('Holes_STR_SUBJECTRF').value = subjectrf;
		document.getElementById('Holes_ADR_CITY').value = city;
		document.getElementById('recognized_address_str').innerHTML = subjectrf + (city.length && city != subjectrf ? ', ' + city : '') + (otherstr.length ? ', ' : '');
		document.getElementById('other_address_str').innerHTML = otherstr;
	});
}
