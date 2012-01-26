<?
$this->pageTitle=Yii::app()->name . ' - Справочник ГИБДД';
$this->title=$model->gibdd_name;
?>	
	<!-- левая колоночка -->
	<div class="lCol">
	
		<div id="ymapcontainer_big"><div align="right"><span class="close" onclick="document.getElementById('ymapcontainer_big').style.display='none';$('#col').css('marginBottom',0)">&times;</span></div><div id="ymapcontainer_big_map"></div></div>
		<?if($model->lat && $model->lng):?><div id="ymapcontainer" class="ymapcontainer"></div><?endif;?>
		<script type="text/javascript">
			var map_centery = <?= $model->lat ?>;
			var map_centerx = <?= $model->lng ?>;
			var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
			YMaps.Events.observe(map, map.Events.DblClick, function () { toggleMap(); } );
			map.enableScrollZoom();
			map.setCenter(new YMaps.GeoPoint(map_centerx, map_centery), 14);			
			var placemark = new YMaps.Placemark(new YMaps.GeoPoint(map_centerx, map_centery), { hideIcon: false, hasBalloon: false } );
			YMaps.Events.observe(placemark, placemark.Events.Click, function () { toggleMap(); } );
			map.addOverlay(placemark);
		</script>
	</div>
	<!-- /левая колоночка -->
	
	<!-- правая колоночка -->
	<div class="rCol"> 
<div class="news-detail">

				<h2><?php echo $model->gibdd_name; ?></h2>
				
						<div style="clear:both"></div>
		 				ФИО:&nbsp;<?php echo $model->fio; ?><br />
	  		
	 				Должность:&nbsp;<?php echo $model->post; ?><br />
	  		
	 				Адрес:&nbsp;<?php echo $model->address; ?><br />
	  		
	 				Телефон дежурной части:&nbsp;<?php echo $model->tel_degurn; ?><br />
	  		
	 				Телефон доверия:&nbsp;<?php echo $model->tel_dover; ?><br />
	  		
	 				Сайт:&nbsp;<?php echo $model->link; ?><br />

<?php if (!Yii::app()->user->isGuest && (Yii::app()->user->id==$model->author_id || Yii::app()->user->level > 50)) : ?>
<br/>
<?php echo CHtml::link('редактировать', array('update','id'=>$model->id)); ?>
<?php endif; ?>	  		
	  		
		</div>

<?php /* $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'gibdd_name',
		'address',
		'fio',
		'fio_dative',
		'post',
		'post_dative',
		'tel_degurn',
		'tel_dover',
	),
)); */ ?>
	</div>
