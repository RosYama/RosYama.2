<div id="ymapcontainer_big"><div align="right"><span class="close" onclick="toggleMap();">&times;</span></div><div id="ymapcontainer_big_map"></div></div>
		<div id="ymapcontainer" class="ymapcontainer"></div>
		<?php Yii::app()->clientScript->registerScript('initmap',<<<EOD
		PlaceMarks=new Array();
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
			
				
EOD
,CClientScript::POS_HEAD);
?>
		<script type="text/javascript">	
		
		function GetPlacemarks(map)
			{
			var bAjaxInProgress=false;			
				if(!bAjaxInProgress)
				{
					bAjaxInProgress = true;
					var addr='/holes/ajaxMap/?user_id=<?php echo $user->id; ?>&jsoncallback=?';
					//alert(addr);
					jQuery.getJSON(addr, function(data) {
						bAjaxInProgress = false;				
						for (i=0;i<data.markers.length;i++){			
							SetMarker(map, data.markers[i].id, data.markers[i].type, data.markers[i].lat, data.markers[i].lng, data.markers[i].state);  
						}
					});
					
				}
				
			}		
		
			var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
			YMaps.Events.observe(map, map.Events.Click, function () { toggleMap(); } );
			map.enableScrollZoom();
			map.setCenter(new YMaps.GeoPoint(37.64, 55.76), 14);	
			GetPlacemarks(map);
			startpoints=new Array;
			<?php foreach ($area as $ind=>$shape) : ?>
			startpoints[<?php echo $ind; ?>]=[
				<?php foreach ($shape->points as $i=>$point) {
					echo 'new YMaps.GeoPoint('.$point->lng.','.$point->lat.')';
					if ($i<count($shape->points)-1) echo ',';
				} ?>
			];
			<?php endforeach; ?>
			bounds = new Array();
			var polygons = new Array();
			for (i=0;i<startpoints.length;i++){
				polygons[i] = new YMaps.Polygon(startpoints[i]);
				var style = new YMaps.Style("default#greenPoint");
				style.polygonStyle = new YMaps.PolygonStyle();
				style.polygonStyle.fill = false;
				style.polygonStyle.outline = true;
				style.polygonStyle.strokeWidth = 4;
				style.polygonStyle.strokeColor = "ff000055"; 
				style.polygonStyle.fillColor = "ff000055";
				polygons[i].setStyle(style);                           

				map.addOverlay(polygons[i]);
				for (ii=0;ii<startpoints[i].length;ii++) bounds.push(startpoints[i][ii]);
				
			}	
				map.setBounds (new YMaps.GeoCollectionBounds(bounds));	
				
		</script>