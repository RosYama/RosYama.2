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
	}
	var PlaceMarks2=PlaceMarks;
		for (id in PlaceMarks2){
			map2.addOverlay(PlaceMarks2[id]);
		} 

		var bounds2 = new Array;		
		for (ind in polygons){
			map2.addOverlay(polygons[ind]);
			 var points=polygons[ind].getPoints();
					for (i=0;i<points.length;i++){
						bounds2.push(points[i]);
					}
		} 	
		map2.setBounds (new YMaps.GeoCollectionBounds(bounds2));
}

function makeMapSmall()
{
	var a = document.getElementById('ymapcontainer_big');
	a.style.display = 'none';
	for (id in PlaceMarks){
			map.addOverlay(PlaceMarks[id]);
		}
	for (i in polygons){
			map.addOverlay(polygons[i]);
		} 	
}

function selectAll(obj) {
  obj.focus()
  obj.select()
}



