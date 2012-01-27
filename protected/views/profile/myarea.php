<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holes-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>Array ('enctype'=>'multipart/form-data'),
)); ?>
<?php echo $form->errorSummary($model); ?>

	<!-- левая колоночка -->
	<div class="lCol">
		<div id="point_fields">
		<?php foreach ($model->hole_area as $i=>$shape) : ?>
			<?php $this->renderPartial('_area_point_fields',array('shape'=>$shape, 'i'=>$shape->ordering, 'form'=>$form)); ?>
		<?php endforeach; ?>
		</div>
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
			<p><a href="#" id="add_shape">Добавить прямоугольник</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Для удаления прямоугольника кликните по нему 2 раза</p>
			<div id="ymapcontainer" class="ymapcontainer"></div>
			<?php Yii::app()->clientScript->registerScript('add_shape','
			function reorder(){
			var idopl=0;
         		var inp=$(".shape_ordering");
				inp.each(function() {
					//$(this).val(idopl);
					//polygons[idopl].id=idopl;
					idopl=parseInt($(this).val());					
					});
			return idopl+1;
			}	
			
			jQuery("body").delegate("#add_shape","click",function(){
				ind=reorder();
				jQuery.ajax({"type":"POST","beforeSend":function(){
				 }, "data":"i="+ind,"success":function(html){
					$("#point_fields").append(html);
					addPolygon(ind);
				  },"url":"'.CController::createUrl("myareaAddshape").'","cache":false});				  
			return false;});',
			CClientScript::POS_END);
			?>
			
			<?php 
			Yii::app()->clientScript->registerScript('initmap',<<<EOD
        
        
        function addPolygon(ind){
        			var startpoints=new Array;
					if ($('#UserAreaShapePoints_'+ind+'_0_lat').val()){
						for (i=0;i<4;i++){
						startpoints[i]=new YMaps.GeoPoint($('#UserAreaShapePoints_'+ind+'_'+i+'_lng').val(),$('#UserAreaShapePoints_'+ind+'_'+i+'_lat').val());
						} 
	
						if (startpoints) {
							bounds = new YMaps.GeoCollectionBounds(startpoints);
							map.setBounds (bounds);			
						}	

					}						
					if (startpoints.length==0 && ind > 1) {
							startpoints=[new YMaps.GeoPoint(map.getCenter().getX()-0.05,map.getCenter().getY()-0.05),
									  new YMaps.GeoPoint(map.getCenter().getX()-0.05,map.getCenter().getY()+0.05),
									  new YMaps.GeoPoint(map.getCenter().getX()+0.05,map.getCenter().getY()+0.05),
									  new YMaps.GeoPoint(map.getCenter().getX()+0.05,map.getCenter().getY()-0.05)];		
						}
					else if (startpoints.length==0 && ind <= 1){
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
					}
					
					var style = new YMaps.Style("default#greenPoint");
					style.polygonStyle = new YMaps.PolygonStyle();
					style.polygonStyle.fill = true;
					style.polygonStyle.outline = true;
					style.polygonStyle.strokeWidth = 10;
					style.polygonStyle.strokeColor = "ffff0088"; 
					style.polygonStyle.fillColor = "ff000055";
					    
					polygons[ind] = new YMaps.Polygon(startpoints, {
										style: style,
										hasHint: 0,
										hasBalloon: 0,										
									});
					polygons[ind].id=ind;				
					
					map.addOverlay(polygons[ind]);
					
					polygons[ind].setEditingOptions({
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
							polygons[ind].splicePoints(2, 1, map.converter.mapPixelsToCoordinates(point1));
							polygons[ind].splicePoints(0, 1, map.converter.mapPixelsToCoordinates(point2));
						} 
						if (index==2) {
							var point1 = points[1].setY(points[2].getY()),
								point2 = points[3].setX(points[2].getX());
							polygons[ind].splicePoints(1, 1, map.converter.mapPixelsToCoordinates(point1));
							polygons[ind].splicePoints(3, 1, map.converter.mapPixelsToCoordinates(point2));
						}
						if (index==3) {
							var point1 = points[0].setY(points[3].getY()),
								point2 = points[2].setX(points[3].getX());
							polygons[ind].splicePoints(0, 1, map.converter.mapPixelsToCoordinates(point1));
							polygons[ind].splicePoints(2, 1, map.converter.mapPixelsToCoordinates(point2));
						}
						if (index==0) {
							var point1 = points[3].setY(points[0].getY()),
								point2 = points[1].setX(points[0].getX());
							polygons[ind].splicePoints(3, 1, map.converter.mapPixelsToCoordinates(point1));
							polygons[ind].splicePoints(1, 1, map.converter.mapPixelsToCoordinates(point2));
						}						
						return points[index];						
						}
					});
					
				    polygons[ind].startEditing();
				    
				    YMaps.Events.observe
						(
							polygons[ind],
							polygons[ind].Events.DblClick,
							function (obj)
							{
								//alert(obj.id);
								map.removeOverlay(obj);
								$(".shape_"+obj.id).remove();
								
							}
						)
					
				}
				
			
				var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
				map.enableScrollZoom();
				map.disableDblClickZoom();
				var polygons=new Array;
				
				center = new YMaps.GeoPoint(37.64, 55.76);
				map.setCenter(center, 14);
				//var placemark = new YMaps.Placemark(new YMaps.GeoPoint({},{}), { hideIcon: false, hasBalloon: false });
				//map.addOverlay(placemark);				
				
				var index=0;				
				$(".shape_ordering").each(function() {
					addPolygon(parseInt($(this).val()));
					});
			
			if (polygons.length==0) $('#add_shape').click();
			
			bounds = new Array;
			
			 for (ind in polygons){
			 var points=polygons[ind].getPoints();
					for (i=0;i<points.length;i++){
						bounds.push(points[i]);
					}
            }
            
            map.setBounds (new YMaps.GeoCollectionBounds(bounds));		
			
            function savepolygon(){   
            for (ind in polygons){
				var points=polygons[ind].getPoints();
					for (i=0;i<points.length;i++){
					$('#UserAreaShapePoints_'+ind+'_'+i+'_lat').val(points[i].getY());
					$('#UserAreaShapePoints_'+ind+'_'+i+'_lng').val(points[i].getX());
					}          
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