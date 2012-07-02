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
	
	if (type=="add" || type=="update") {
	map.disableDblClickZoom();	
	YMaps.Events.observe(map, map.Events.DblClick, setCoordValue);	
	}
	if (type=="update") {	
	setCoordValue(map);
	center = new YMaps.GeoPoint($('#GibddHeads_lng').val(), $('#GibddHeads_lat').val());
	map.setCenter(center, zoom, context.YMaps.MapType.MAP);	
	}
	if (type=="update_regional" || type=="update_regional_areaExtend" ){
	//polygon=setPolygon(map, new YMaps.GeoPoint(lat, lon));
	map.disableDblClickZoom();	
	/*YMaps.Events.observe(map, map.Events.DblClick, function(map, ev){		
		if (!polygons.length) {
			//setPolygon(map, new YMaps.GeoPoint(ev.getCoordPoint().getX(), ev.getCoordPoint().getY()));
			}
	});*/	
	if (!polygons.length && type=="update_regional_areaExtend") {
		if (startpoints.length){
			for (i in startpoints)
				addPolygon(map, i , startpoints[i]);
		}
		else polygons=setPolygon(map, new YMaps.GeoPoint(0, 0));		
		}
	}
	$('#newPolygon').click(function() {	
		var defaultpoints=[new YMaps.GeoPoint(map.getCenter().getX()-0.00,map.getCenter().getY()-0.06),
											  new YMaps.GeoPoint(map.getCenter().getX()-0.06,map.getCenter().getY()+0.00),
											  new YMaps.GeoPoint(map.getCenter().getX()+0.00,map.getCenter().getY()+0.06),
											  new YMaps.GeoPoint(map.getCenter().getX()+0.06,map.getCenter().getY()+0.00)];
		addPolygon(map, polygons.length , defaultpoints);
	return false;
	});	
	
	if (defbounds.length){
	bounds=new Array();
		for (i in defbounds)
			for (ii in defbounds[i])
				bounds.push(defbounds[i][ii]);		
		map.setBounds (new YMaps.GeoCollectionBounds(bounds));
	}
	return map;
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
		$('#GibddHeads_lat').val(ev.getCoordPoint().getY());
		$('#GibddHeads_lng').val(ev.getCoordPoint().getX());
	}
	var lon = $('#GibddHeads_lat').val();
	var lat = $('#GibddHeads_lng').val();
	coordpoint = new YMaps.Placemark(new YMaps.GeoPoint(lat, lon), { style: 'default#violetPoint', draggable: true, hasBalloon: false, hideIcon: false });
	
	polygon=setPolygon(map, new YMaps.GeoPoint(lat, lon));
	
	
	YMaps.Events.observe(coordpoint, coordpoint.Events.DragEnd, function (obj) {
		$('#GibddHeads_lat').val(obj.getCoordPoint().getY());
		$('#GibddHeads_lng').val(obj.getCoordPoint().getX());
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
				document.getElementById('GibddHeads_address').value = geo_text.join(',').substr(2);
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
		document.getElementById('GibddHeads_str_subject').value = subjectrf;		
		document.getElementById('recognized_address_str').innerHTML = subjectrf + (city.length && city != subjectrf ? ', ' + city : '') + (otherstr.length ? ', ' : '');
		document.getElementById('other_address_str').innerHTML = otherstr;
	});
}
