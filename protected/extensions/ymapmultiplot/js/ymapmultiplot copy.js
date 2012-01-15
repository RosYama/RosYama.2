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

function GetPlacemarks(map)
{
	if(!bAjaxInProgress)
	{
		bAjaxInProgress = true;
		var mapBounds = map.getBounds();
		jQuery.get
		(
			'/holes/ajaxMap',
			{
				bottom: mapBounds.getBottom(),
				left:   mapBounds.getLeft(),
				top:    mapBounds.getTop(),
				right:  mapBounds.getRight(),
				zoom:  map.getZoom(),
				state:  filter_state,
				type:   filter_type
			},
			function(data)
			{
				bAjaxInProgress = false;
				map.removeAllOverlays();
				//alert (data);
				//alert ('?bottom='+mapBounds.getBottom()+'&left='+mapBounds.getLeft()+'&top='+mapBounds.getTop()+'&right='+mapBounds.getRight());
				eval(data);
			}
		);
	}
}