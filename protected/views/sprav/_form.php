<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'gibdd-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>Array ('enctype'=>'multipart/form-data'),
)); ?>
<?php echo $form->errorSummary($model); ?>

	<? /*<input type="hidden" name="ID" value="<?= $F['ID']['VALUE'] ?>">
	 if($F['FIX_ID']): ?>
		<input type="hidden" name="FIX_ID" value="<?= $F['FIX_ID']['VALUE'] ?>">
	<? elseif($F['GIBDD_REPLY_ID']): ?>
		<input type="hidden" name="GIBDD_REPLY_ID" value="<?= $F['GIBDD_REPLY_ID']['VALUE'] ?>">
	<? endif;*/ ?>

	<!-- левая колоночка -->
	<div class="lCol">

		<div class="f">
		<?php echo $form->labelEx($model,'level'); ?>
		<?php echo $form->dropDownList($model, 'level', $model->userlevelNames);?>
		<?php echo $form->error($model,'level'); ?>
		</div>
	
		<div class="f">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'name'); ?>
			<em class="hint">Пример: ГИБДД по Москве</em>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'gibdd_name'); ?>
			<?php echo $form->textField($model,'gibdd_name',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'gibdd_name'); ?>
			<em class="hint">Пример: Управление ГИБДД ГУ МВД России по г. Москве</em>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'address'); ?>
			<?php echo $form->textField($model,'address',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'address'); ?>
			<em class="hint">Пример: 127473, г. Москва, ул. Садовая-Самотечная, 1</em>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'fio'); ?>
			<?php echo $form->textField($model,'fio',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'fio'); ?>
			<em class="hint">Пример: Ильин Александр Владимирович</em>
		</div>		
		
		<div class="f">
			<?php echo $form->labelEx($model,'fio_dative'); ?>
			<?php echo $form->textField($model,'fio_dative',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'fio_dative'); ?>
			<em class="hint">Пример: Ильину Александру Владимировичу</em>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'post'); ?>
			<?php echo $form->textField($model,'post',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'post'); ?>
			<em class="hint">Пример: Начальник</em>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'post_dative'); ?>
			<?php echo $form->textField($model,'post_dative',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'post_dative'); ?>
			<em class="hint">Пример: Начальнику Управления ГИБДД ГУ МВД России по г. Москве</em>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'tel_degurn'); ?>
			<?php echo $form->textField($model,'tel_degurn',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'tel_degurn'); ?>
			<em class="hint">Пример: (495) 624-31-17</em>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'tel_dover'); ?>
			<?php echo $form->textField($model,'tel_dover',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'tel_dover'); ?>
			<em class="hint">Пример: (495) 624-31-17</em>
		</div>
		
		<?php if ($model->is_regional) : ?>	
		<div class="f">
			<?php echo $form->labelEx($model,'url'); ?>
			<?php echo $form->textField($model,'url',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'url'); ?>
			<em class="hint">Пример: http://www.site.ru/page.html</em>
		</div>		
		<?php endif; ?>	
		<div class="f">
			<?php echo $form->labelEx($model,'url_priemnaya'); ?>
			<?php echo $form->textField($model,'url_priemnaya',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'url_priemnaya'); ?>
			<em class="hint">Пример: http://www.site.ru/page.html</em>
		</div>
	
		
		<?php echo $form->textField($model,'lat'); ?>
		<?php echo $form->textField($model,'lng'); ?>
		<?php echo $form->hiddenField($model,'str_subject'); ?>
	</div>
	<!-- /левая колоночка -->
	<!-- правая колоночка -->
	<div class="rCol"> 
	<div class="f">
	<?php if (!$model->is_regional) : ?>	
	<p class="tip">
Поставьте метку на карте двойным щелчком мыши
<span class="required">*</span>
</p>
		<div class="bx-yandex-search-layout" style="padding-bottom: 0px;">
			<div class="bx-yandex-search-form" style="padding-bottom: 0px;">				
					<p>Введите адрес отдела ГИБДД для быстрого поиска</p>
					<input type="text" id="address_inp" name="address" class="textInput" value="" style="width: 300px;" />
					<input type="submit" value="Искать" onclick="jsYandexSearch_MAP_DzDvWLBsil.searchByAddress($('#address_inp').val()); return false;" />
					<a style="display:none;" id="clear_result_link" href="#" onclick="clearSerchResults('MAP_DzDvWLBsil', JCBXYandexSearch_arSerachresults); document.getElementById('address_inp').value=''; return false;">Очистить</a>				
			</div>		
			<div class="bx-yandex-search-results" id="results_MAP_DzDvWLBsil"></div>
		</div>	
			<span id="recognized_address_str" title="Субъект РФ и населённый пункт"></span>
			<span id="other_address_str"></span>				
		<?php endif; ?>	
		
		<div class="bx-yandex-view-layout">
			<div class="bx-yandex-view-map">
		<?php if ($model->isNewRecord && $model->lat==0 && $model->lng==0) $maptype='add'; else $maptype='update'; 
		if ($model->is_regional) { if ($model->areas) $maptype='update_regional_areaExtend'; else $maptype='update_regional'; }
		$jsBounds='';
		if (!$model->areas) 
			foreach ($model->subject->gibdd->areas as $i=>$area){
				$jsBounds.=" defbounds[".$i."]=[".$area->JsAreaPoints."];";
			}
		?>
		<?php 
		$jsPoints='';
		if ($model->areas)			
			foreach ($model->areas as $i=>$area){
				$jsPoints.=" startpoints[".$i."]=[".$area->JsAreaPoints."];";
			}
		
		Yii::app()->clientScript->registerScript('initmap',"
		
		var polygons=new Array;	
		var startpoints=new Array;
		var map;
		var defaultpoints;
		var defbounds=new Array;
		".$jsBounds."
		function addPolygon(map, i , points){						
						
						var style = new YMaps.Style('default#greenPoint');
						style.polygonStyle = new YMaps.PolygonStyle();
						style.polygonStyle.fill = true;
						style.polygonStyle.outline = true;
						style.polygonStyle.strokeWidth = 4;
						style.polygonStyle.strokeColor = 'ff000070'; 
						style.polygonStyle.fillColor = '1370AA55';
							
						polygons[i] = new YMaps.Polygon(points, {
											style: style,
											hasHint: 0,
											hasBalloon: 0,										
										});
			
						polygons[i].id=i;
						map.addOverlay(polygons[i]);
						
						polygons[i].setEditingOptions({
							drawing: true,
							maxPoints: 100000,
							dragging:true,	
							
						});
						
						polygons[i].startEditing();	
						
						YMaps.Events.observe
						(
							polygons[i],
							polygons[i].Events.DblClick,
							function (obj)
							{
								//alert (polygons.length);
								if (polygons.length > 1){
									map.removeOverlay(obj);
									//delete polygons[i]; 
									polygons.splice (i, 1);
									}
								
							}
						)
						
		}
		
		function setPolygon(map, center){
					
					if (center.getX() !=0)
					defaultpoints=[new YMaps.GeoPoint(center.getX()-0.00,center.getY()-0.06),
											  new YMaps.GeoPoint(center.getX()-0.06,center.getY()+0.00),
											  new YMaps.GeoPoint(center.getX()+0.00,center.getY()+0.06),
											  new YMaps.GeoPoint(center.getX()+0.06,center.getY()+0.00)];
					else 
						defaultpoints=[new YMaps.GeoPoint(map.getCenter().getX()-0.00,map.getCenter().getY()-0.06),
											  new YMaps.GeoPoint(map.getCenter().getX()-0.06,map.getCenter().getY()+0.00),
											  new YMaps.GeoPoint(map.getCenter().getX()+0.00,map.getCenter().getY()+0.06),
											  new YMaps.GeoPoint(map.getCenter().getX()+0.06,map.getCenter().getY()+0.00)];
					
					var bounds = new Array();					
					".$jsPoints."		
					
					if (!startpoints.length) startpoints[0]=defaultpoints;
					
					for (i in startpoints){					
						for (ii=0;ii<startpoints[i].length;ii++){
							bounds.push(startpoints[i][ii]);
						}
						map.setBounds (new YMaps.GeoCollectionBounds(bounds));
						addPolygon(map, i , startpoints[i]);
						//alert (i);
							
				    }
				    return polygons;

		}
		
		if (window.attachEvent) // IE
			window.attachEvent('onload', function(){init_MAP_DzDvWLBsil(null,'{$maptype}');});
		else if (window.addEventListener) // Gecko / W3C
			window.addEventListener('load', function(){init_MAP_DzDvWLBsil(null,'{$maptype}'); }, false);
		else
			window.onload = function(){init_MAP_DzDvWLBsil(null,'{$maptype}'); };

			
"
,CClientScript::POS_HEAD);
?>
<?php Yii::app()->clientScript->registerScript('savegibdd',<<<EOD
		
		
		$('#gibdd-form').submit(function() {
			if (!polygons) return false;
			
			for (i in polygons){	
				var points=polygons[i].getPoints();				
					for (ii=0;ii<points.length;ii++){
						$(this).append('<input type="hidden" name="GibddAreaPoints['+i+']['+ii+'][lat]" value="'+points[ii].getY()+'" /><input type="hidden" name="GibddAreaPoints['+i+']['+ii+'][lng]" value="'+points[ii].getX()+'" />');
					}
			}	
			return true;
		});		

			
EOD
,CClientScript::POS_READY);
?>
<?php if ($model->is_regional) : ?>
<a href="#" id="newPolygon">Добавить область</a>
<?php endif;?>
<div id="BX_YMAP_MAP_DzDvWLBsil" style="width:100%; height:400px;" class="bx-yandex-map">загрузка карты...</div>		
			</div>
		</div>
		<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />

	</div>
		
	</div>
	<!-- /правая колоночка -->	
	<div class="addSubmit">
		<div class="container">
			<p></p>
			<div class="btn" onclick="$(this).parents('form').submit();">
				<a class="addFact"><i class="text"><?php echo $model->isNewRecord ? 'Добавить' : 'Сохранить'; ?></i><i class="arrow"></i></a>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->