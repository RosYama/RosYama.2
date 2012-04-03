<!-- lat 59.9394 lon 30.3154 -->

<?
$this->pageTitle=Yii::app()->name . ' :: Карта дефектов';
?>
<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>
  <div class="head">
		<div class="container">
<div class="lCol">
		<a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
</div>
						<div class="rCol">
				<script language="javascript">
<!--

function GetCheckNull()
{

}
-->
</script>
<h1>Карта дефектов 2.1</h1>

<?php $form=$this->beginWidget('CActiveForm',Array(
	'id'=>'map-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="filterCol filterStatus">
<p class="title">Показать дефекты со статусом</p>
<?php foreach ($model->allstatesMany as $alias=>$name) : ?>
	<label><span class="<?php echo $alias; ?>"><input id="chn0" name="Holes[STATE][]" type="checkbox"  value="<?php echo $alias; ?>" /></span><ins><?php echo $name; ?></ins></label>
<?php endforeach; ?>	
</div>
<div class="filterCol filterType">
<p class="title">Показать тип дефектов</p>
<?php foreach ($types as $i=>$type) : ?>
<label class="col2"><span><input id="ch0" name="Holes[type][]" type="checkbox" value="<?php echo $type->id; ?>"   /></span><ins class="<?php echo $type->alias; ?>"><?php echo $type->name; ?></ins></label>
<?php endforeach; ?>
<input id="MAPLAT" name="MAPLAT" type="hidden" value="" />
<input id="MAPZOOM" name="MAPZOOM" type="hidden" value="" />


</div>
<div class="submit">
	<span class="submit_left">
		<input class="inputBtn" type="submit" name="button" id="button" value="Показать" />
		<input type="reset" class="inputBtn" name="reset" id="reset_button" value="Сбросить" type="button" />
	</span>

	<span class="submit_right">
		Выберите тип карты:&nbsp;
		<ul class="tabs">
		<li><a class="profileBtn" href="#map_osm">osm</a></li>
		<li><a class="profileBtn" href="#map_google" id="showMap">google</a></li>
		<li><a class="profileBtn" href="#map_yandex">yandex</a></li>
		</ul>
	</span>
</div>
<?php $this->endWidget(); ?>			</div>
		</div>
	</div>
	<div class="mainCols">
			
<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>

<div class="container">

<!-- Start tab container -->
<div class="tab_container">

    <div id="map_osm" class="tab_content">

	<!-- начало контейнера карты osm -->

	<div id="map_osm_container" style="height: 500px; border: 1px solid #ccc;">

	<script src="http://code.leafletjs.com/leaflet-0.3.1/leaflet.js"></script>
	<script>
		var map = new L.Map('map_osm_container');

		var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/997/256/{z}/{x}/{y}.png',
			cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade',
			cloudmade = new L.TileLayer(cloudmadeUrl, {maxZoom: 18, attribution: cloudmadeAttribution});

		map.setView(new L.LatLng(59.9394, 30.3154), 13).addLayer(cloudmade);

		var markerLocation = new L.LatLng(59.9394, 30.3154),
			marker = new L.Marker(markerLocation);

		map.addLayer(marker);

		map.on('click', onMapClick);

		function onMapClick(e) {

			var latlngStr = '(' + e.latlng.lat.toFixed(3) + ', ' + e.latlng.lng.toFixed(3) + ')';

			popup.setLatLng(e.latlng);
			popup.setContent("You clicked the map at " + latlngStr);
			map.openPopup(popup);
		}

	</script>

	</div>
	<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />
	<!-- конец контейнера карты osm -->
    </div>

<div id="map_google" class="tab_content">
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" language="javascript">
    //<![CDATA[
    $(document).ready(function() {

      function initialize() {
        var myLatlng = new google.maps.LatLng(-31.952222,115.858889);
        var myOptions = {
          zoom: 14,
          center: myLatlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        var contentString = '<div id="content">'+
            '<h1>Perth, Western Australia</h1>'+
            '<div>'+
            '<p><b>Perth</b> is the capital and largest city of the Australian state of Western Australia and the fourth most populous city in Australia</p>'+
            '<p>Attribution: Perth, Western Australia, <a href="http://en.wikipedia.org/wiki/Perth,_Western_Australia">'+
            'http://en.wikipedia.org/wiki/Perth,_Western_Australia</a></p>'+
            '</div>'+
            '</div>';

        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 300
        });

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: 'Perth, Western Australia'
        });
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map,marker);
        });

      }
        // Function added to help reset map and container boundaries
        $("#showMap").click(function() {
        $("#map_google").css({'display':'block'});
        $("#map_canvas").css({'width':'988px', 'height':'500px'});
        initialize();
        //alert('showMap Clicked!');
        });

     initialize(); 

    });
    //]]>
    </script>
    <div id="map_canvas" style="width:988px;height:500px;"></div>
	<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />
    </div>

    <div id="map_yandex" class="tab_content">

<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>

<div class="bx-yandex-search-layout">
<!--
	<div class="bx-yandex-search-form">
		<form id="search_form_MAP_DzDvWLBsil" name="search_form_MAP_DzDvWLBsil" onsubmit="jsYandexSearch_MAP_DzDvWLBsil.searchByAddress(this.address.value); return false;">
			<p>Введите адрес места для быстрого поиска</p>
			<input type="text" id="address_inp" name="address" class="textInput" value="" style="width: 300px;" />
			<input type="submit" value="Искать" />
			<a style="display:none;" id="clear_result_link" href="#" onclick="clearSerchResults('MAP_DzDvWLBsil', JCBXYandexSearch_arSerachresults); document.getElementById('address_inp').value=''; return false;">Очистить</a>
		</form>
	</div>
-->
<div class="bx-yandex-search-results" id="results_MAP_DzDvWLBsil"></div>
		

<div class="bx-yandex-view-layout" style="border: 1px solid #ccc;">

	<div class="bx-yandex-view-map">
	<script type="text/javascript">
	history.navigationMode = 'compatible';
	$(document).ready( function(){
		init_MAP_DzDvWLBsil();
	        }

	);
	</script>

<?php
	$this->widget('application.extensions.ymapmultiplot.YMapMultiplot', array(
		'key'=>$this->mapkey,
		'id' => 'BX_YMAP_MAP_DzDvWLBsil',//id of the <div> container created
		'label' => 'Тест', //Title for bubble. Used if you are plotting multiple locations of same business
		'address' =>  Array(), //Array of AR objects
		'width'=>'100%',
		'height'=>'500px',						   
		//'notshow'=>true
	));
?>
	</div>
</div>
<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />
</div>

<!-- конец контейнера карты yandex -->


    </div>

</div>
<!-- End tab container -->

</div>

			</div>
	</div>
	</div>
