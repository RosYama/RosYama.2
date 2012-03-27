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
		loc[2] = loc[2].split(':');
		if (loc[2][1]=='map') map.setType(YMaps.MapType.MAP);
		if (loc[2][1]=='sat') map.setType(YMaps.MapType.SATELLITE);
		if (loc[2][1]=='sat,skl') map.setType(YMaps.MapType.HYBRID);
		
	}
	if (type=="addhole" || type=="updatehole") {
	map.disableDblClickZoom();
	YMaps.Events.observe(map, map.Events.DblClick, setCoordValue);
	}
	if (type=="updatehole") {	
	setCoordValue(map);
	}
	GetPlacemarks(map);	
}
      

function SetMarker(map,id,type,lat,lng,state)
{
					var s = new YMaps.Style();
					s.iconStyle = new YMaps.IconStyle();
					s.iconStyle.href = "/images/st1234/"+type+"_"+state+".png";
					s.iconStyle.size = new YMaps.Point(28, 30);
					s.iconStyle.offset = new YMaps.Point(-14, -30);
					if (! (id in PlaceMarks)){
						PlaceMarks[id] = new YMaps.Placemark(new YMaps.GeoPoint(lat, lng), { hasHint: false, hideIcon: true, hasBalloon: false, style: s });
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

var clusters=new Array;

function SetCluster(map,count,lat,lng,i)
{
					var s = new YMaps.Style();
					var size=count/50+50;
					//var template = new YMaps.Template('<div style="cursor:pointer;position:relative;line-height:'+size+'px;height:'+size+'px;width:'+size+'px;text-align:center;background:url($[style.iconStyle.href]) no-repeat;font-weight:bold;font-size:$[textSize|12]px;color:$[textColor|#000]">'+count+'</div>');
					var template = new YMaps.Template("<div style='cursor:pointer; text-align:center;'>\
                <img style=\"height:"+size+"px;width:"+size+"px;\" src=\"$[style.iconStyle.href]\"\/>\
                <div style=\"font-weight:bold;font-size:$[textSize|12]px;color:$[textColor|#000]; position:absolute; z-index:+1000; top:"+(size/2-5)+"px; text-align:center; width:100%;\">"+count+"</div></div>");   
					s.iconStyle = new YMaps.IconStyle(template);
					s.iconStyle.href = "http://gmaps-utility-library.googlecode.com/svn/trunk/markerclusterer/images/m1.png";					
					s.iconStyle.size = new YMaps.Point(size, size);
					s.iconStyle.offset = new YMaps.Point(-size/2, -size/2);
						clusters[i] = new YMaps.Placemark(new YMaps.GeoPoint(lat, lng), { hasHint: false, hideIcon: true, hasBalloon: false, style: s });
						map.addOverlay(clusters[i]);
						YMaps.Events.observe
						(
							clusters[i],
							clusters[i].Events.Click,
							function (obj)
							{
								center = new YMaps.GeoPoint(lat,lng);					
								map.setCenter(center);
								map.setZoom(map.getZoom() + 1);	
							}
						)


}

function GetPlacemarks(map)
{

	if(!bAjaxInProgress)
	{
		bAjaxInProgress = true;
		var mapBounds = map.getBounds();
		var exclude_id='';		
		if ($('#Exclude_id').val()) exclude_id='&exclude_id='+$('#Exclude_id').val();
		var addr='/holes/ajaxMap/?bottom='+mapBounds.getBottom()+'&left='+mapBounds.getLeft()+'&top='+mapBounds.getTop()+'&'+jQuery('#map-form').serialize()+'&right='+mapBounds.getRight()+'&zoom='+map.getZoom()+exclude_id+'&jsoncallback=?';
		//alert(addr);
		jQuery.getJSON(addr, function(data) {
			bAjaxInProgress = false;				
			PlaceMarks=new Array;  
			map.removeAllOverlays();
			for (i=0;i<data.markers.length;i++){			
				SetMarker(map, data.markers[i].id, data.markers[i].type, data.markers[i].lat, data.markers[i].lng, data.markers[i].state);  
				//alert (data.markers[i].type);
			}
			clusters=new Array;
			for (i=0;i<data.clusters.length;i++){			
				SetCluster(map, data.clusters[i].count, data.clusters[i].lat, data.clusters[i].lng, i);  
			}
			
			//clusterer.setMarkers(PlaceMarks); // add markers to clusterer
           // clusterer.repaint(); // update clusterer on map
		});
		
	}
	
}

function BX_SetPlacemarks_MAP_DzDvWLBsil(map)
{
	var arObjects = {PLACEMARKS:[],POLYLINES:[]};	

	YMaps.Events.observe(map, map.Events.MoveEnd, function() {
		var res = "{ 'center': '" + map.getCenter() + "', 'zoom': '" + map.getZoom() + "' }"
		document.cookie = "map_settings="+res
		res = "center:" + map.getCenter() + ";zoom:" + map.getZoom() + ";type:" + map.getType().getLayers();
		var loc = new String(document.location);
		loc = loc.split('#');
		document.location = loc[0] + '#' + res;
		GetPlacemarks(map);
	} );
	
	YMaps.Events.observe(map, map.Events.Move, function() { GetPlacemarks(map);	} );

	YMaps.Events.observe(map, map.Events.Update, function() {
		var res = "{ 'center': '" + map.getCenter() + "', 'zoom': '" + map.getZoom() + "' }"
		document.cookie = "map_settings="+res
		res = "center:" + map.getCenter() + ";zoom:" + map.getZoom() + ";type:" + map.getType().getLayers();
		var loc = new String(document.location);
		loc = loc.split('#');
		document.location = loc[0] + '#' + res;
		GetPlacemarks(map);
	} );
	
	YMaps.Events.observe(map, map.Events.TypeChange, function() { 
		var res = "{ 'center': '" + map.getCenter() + "', 'zoom': '" + map.getZoom() + "' }"
		document.cookie = "map_settings="+res
		res = "center:" + map.getCenter() + ";zoom:" + map.getZoom() + ";type:" + map.getType().getLayers();
		var loc = new String(document.location);
		loc = loc.split('#');
		document.location = loc[0] + '#' + res;
	} );
	
	
	//GetPlacemarks(map);
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
	else {
		var ev=false; 
		map.setCenter(new YMaps.GeoPoint($('#Holes_LONGITUDE').val(), $('#Holes_LATITUDE').val()));
		}
	var lon = $('#Holes_LATITUDE').val();
	var lat = $('#Holes_LONGITUDE').val();
	coordpoint = new YMaps.Placemark(new YMaps.GeoPoint(lat, lon), { style: 'default#violetPoint', draggable: true, hasBalloon: false, hideIcon: false });
	YMaps.Events.observe(coordpoint, coordpoint.Events.DragEnd, function (obj) {
		$('#Holes_LATITUDE').val(obj.getCoordPoint().getY());
		$('#Holes_LONGITUDE').val(obj.getCoordPoint().getX());
		geocodeOnSetCoordValue(true);
	});
	map.addOverlay(coordpoint);	
	geocodeOnSetCoordValue(ev);
}

function geocodeOnSetCoordValue(ev)
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
		
		if (ev){
		jQuery.ajax({'type':'POST','url':'/holes/territorialGibdd','data':$('#holes-form').serialize(),'beforeSend':function(){
														$("#Holes_gibdd_id").attr("disabled", "true");
													 },'complete':function(){
														$("#Holes_gibdd_id").removeAttr("disabled");
													 },'cache':false,'success':function(html){
													 $("#Holes_gibdd_id").html(html);
													 //jQuery("#gibdd_form").html(html)
													 }});
		}
		
	});
}
