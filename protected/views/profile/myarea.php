<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holes-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>Array ('enctype'=>'multipart/form-data'),
)); ?>
<?php echo $form->errorSummary($model); ?>

	<!-- левая колоночка -->
	<div class="lCol">
		<?php for ($i=0;$i<4;$i++) : 
		if (isset($model->hole_area[$i])) $areamodel=$model->hole_area[$i];
		else $areamodel=new UserHoleArea; ?>
			<?php echo $form->hiddenField($areamodel,"[$i]id"); ?>
			<?php echo $form->hiddenField($areamodel,"[$i]lat"); ?>
			<?php echo $form->hiddenField($areamodel,"[$i]lng"); ?>
			<?php echo $form->hiddenField($areamodel,"[$i]point_num",Array('value'=>$i)); ?>
		<?php endfor; ?>
	</div>
	<!-- /левая колоночка -->
	
	<!-- правая колоночка -->
	<div class="rCol"> 
	<div class="f">	
	<?php if (!$model->hole_area) : ?>
	<h2>На карте, отметте прямоугольником границы своего участка</h2>
	<?php endif; ?>
		<div class="bx-yandex-view-layout">
			<div class="bx-yandex-view-map">
			<div id="ymapcontainer" class="ymapcontainer"></div>
			<?php Yii::app()->clientScript->registerScript('initmap',<<<EOD
				var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
				map.enableScrollZoom();
				var startpoints=new Array;
				if (YMaps.location) {
					center = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);
					
					startpoints=[		 new YMaps.GeoPoint(YMaps.location.longitude-0.05,YMaps.location.latitude-0.05),
									  new YMaps.GeoPoint(YMaps.location.longitude-0.05,YMaps.location.latitude+0.05),
									  new YMaps.GeoPoint(YMaps.location.longitude+0.05,YMaps.location.latitude+0.05),
									  new YMaps.GeoPoint(YMaps.location.longitude+0.05,YMaps.location.latitude-0.05)];
				
					if (YMaps.location.zoom) {
						zoom = YMaps.location.zoom;
					}				
					
				}else {
					center = new YMaps.GeoPoint(37.64, 55.76);
					
					startpoints=[	  new YMaps.GeoPoint(37.7,55.7),
									  new YMaps.GeoPoint(37.7,55.8),
									  new YMaps.GeoPoint(37.8,55.8),
									  new YMaps.GeoPoint(37.8,55.7)]
				}
				
				// Установка для карты ее центра и масштаба
				map.setCenter(center, zoom);
				
				//map.setCenter(new YMaps.GeoPoint({},{}), 14);
				//var placemark = new YMaps.Placemark(new YMaps.GeoPoint({},{}), { hideIcon: false, hasBalloon: false });
				//map.addOverlay(placemark);				
				
				
				if ($('#UserHoleArea_0_lat').val()){
					for (i=0;i<4;i++){
					startpoints[i]=new YMaps.GeoPoint($('#UserHoleArea_'+i+'_lng').val(),$('#UserHoleArea_'+i+'_lat').val());
					} 

					if (startpoints) {
						bounds = new YMaps.GeoCollectionBounds(startpoints);
						map.setBounds (bounds);			
					}	

				}		
				
						
				var polygon = new YMaps.Polygon(startpoints);
				
				

				var style = new YMaps.Style("default#greenPoint");
				style.polygonStyle = new YMaps.PolygonStyle();
				style.polygonStyle.fill = true;
				style.polygonStyle.outline = true;
				style.polygonStyle.strokeWidth = 10;
				style.polygonStyle.strokeColor = "ffff0088"; 
				style.polygonStyle.fillColor = "ff000055";
				polygon.setStyle(style);                                

				map.addOverlay(polygon);
					

				
				// Включение режима редактирования
			polygon.setEditingOptions({
				drawing: false,
				maxPoints: 4,
				dragging:true,
				onClick: function (polygon, pointIndex, coordPath) {
                return false;
            	},
            	onDblClick: function (polygon, pointIndex, coordPath) {
                return false;
            	},
            	menuManager: function (index, menuItems) {
					return false;
				},
				onPointDragging: function (points, index) {                
                if (index==1) {
                    var point1 = points[2].setY(points[1].getY()),
                        point2 = points[0].setX(points[1].getX());
                    polygon.splicePoints(2, 1, map.converter.mapPixelsToCoordinates(point1));
                    polygon.splicePoints(0, 1, map.converter.mapPixelsToCoordinates(point2));
                } 
                if (index==2) {
                    var point1 = points[1].setY(points[2].getY()),
                        point2 = points[3].setX(points[2].getX());
                    polygon.splicePoints(1, 1, map.converter.mapPixelsToCoordinates(point1));
                    polygon.splicePoints(3, 1, map.converter.mapPixelsToCoordinates(point2));
                }
                if (index==3) {
                    var point1 = points[0].setY(points[3].getY()),
                        point2 = points[2].setX(points[3].getX());
                    polygon.splicePoints(0, 1, map.converter.mapPixelsToCoordinates(point1));
                    polygon.splicePoints(2, 1, map.converter.mapPixelsToCoordinates(point2));
                }
                if (index==0) {
                    var point1 = points[3].setY(points[0].getY()),
                        point2 = points[1].setX(points[0].getX());
                    polygon.splicePoints(3, 1, map.converter.mapPixelsToCoordinates(point1));
                    polygon.splicePoints(1, 1, map.converter.mapPixelsToCoordinates(point2));
                }
                //$('#tempo').html(points[1].getY());
                return points[index];
                
            	}
			});	
            polygon.startEditing();	
            
            function savepolygon(){            
            var points=polygon.getPoints();
            	for (i=0;i<points.length;i++){
            	$('#UserHoleArea_'+i+'_lat').val(points[i].getY());
            	$('#UserHoleArea_'+i+'_lng').val(points[i].getX());
            	}            
            
            }
				
EOD
,CClientScript::POS_END);
?>
			</div>
		</div>
		<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />
	</div>			
		
		
	</div>
	<!-- /правая колоночка -->
	<div class="addSubmit">
		<div class="container">
			<div class="btn" onclick="
			savepolygon();
			$(this).parents('form').submit();
			">
				<a class="addFact"><i class="text">Сохранить</i><i class="arrow"></i></a>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->