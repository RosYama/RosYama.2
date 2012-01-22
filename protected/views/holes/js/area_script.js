var map_big = false;
var map2    = null;

function toggleMap()
{
	if(map_big)
	{
		makeMapSmall();
	}
	else
	{
		makeMapBig();
	}
	map_big = !map_big;
	if($('#col').height()<$('#ymapcontainer_big').height())
	{
		$('#col').css('marginBottom',200)
	}
}

function makeMapBig()
{
	var a = document.getElementById('ymapcontainer_big');
	a.style.display = 'block';
	if(!map2)
	{
		map2 = new YMaps.Map(YMaps.jQuery("#ymapcontainer_big_map")[0]);
		YMaps.Events.observe(map2, map2.Events.Click, function () { toggleMap(); } );
		map2.enableScrollZoom();
		map2.setCenter(new YMaps.GeoPoint(37.64, 55.76), 14);
		var bounds2=bounds;
		map2.setBounds (bounds2);
	}
	var PlaceMarks2=PlaceMarks;
		for (id in PlaceMarks2){
			map2.addOverlay(PlaceMarks2[id]);
		} 
		var polygon2=polygon;		
		map2.addOverlay(polygon2);			
}

function makeMapSmall()
{
	var a = document.getElementById('ymapcontainer_big');
	a.style.display = 'none';
	for (id in PlaceMarks){
			map.addOverlay(PlaceMarks[id]);
		} 
	map.addOverlay(polygon);
}

function selectAll(obj) {
  obj.focus()
  obj.select()
}



