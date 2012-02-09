<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holes-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>Array ('enctype'=>'multipart/form-data'),
)); ?>
<?php echo $form->errorSummary($answer); ?>

	<? /*<input type="hidden" name="ID" value="<?= $F['ID']['VALUE'] ?>">
	 if($F['FIX_ID']): ?>
		<input type="hidden" name="FIX_ID" value="<?= $F['FIX_ID']['VALUE'] ?>">
	<? elseif($F['GIBDD_REPLY_ID']): ?>
		<input type="hidden" name="GIBDD_REPLY_ID" value="<?= $F['GIBDD_REPLY_ID']['VALUE'] ?>">
	<? endif;*/ ?>

	<!-- левая колоночка -->
	<div class="lCol">
		<!-- тип дефекта -->
		<div class="f">
			<?php echo $model->type->name; ?>
		</div>
		
		<!-- адрес -->
		<div class="f">
			<?php echo $model->ADDRESS; ?>			
		</div>
		
		<!-- камент -->
		<div class="f">
			<?php echo $model->COMMENT1; ?>
		</div>

		
		<!-- фотки -->
		<div class="f">
			<?php echo $form->labelEx($answer,'uppload_files'); ?>
			<span class="comment">Размер каждого загружаемого файла не должен превышать 2 Мб. Суммарный размер файлов не должен превышать 8 Мб.</span>			
			<?php $this->widget('CMultiFileUpload',array('accept'=>'gif|jpg|png|pdf|txt', 'model'=>$answer, 'attribute'=>'uppload_files', 'htmlOptions'=>array('class'=>'mf'), 'denied'=>Yii::t('mf','Невозможно загрузить этот файл'),'duplicate'=>Yii::t('mf','Файл уже существует'),'remove'=>Yii::t('mf','удалить'),'selected'=>Yii::t('mf','Файлы: $file'),)); ?>						
		</div>
		
		<!-- анкета -->
		<div class="f chekboxes">
			<?php echo $form->labelEx($answer,'results'); ?>
			<?php echo $form->checkBoxList($answer,'results',CHtml::listData( HoleAnswerResults::model()->findAll(Array('order'=>'ordering','condition'=>'published=1')), 'id', 'name' ),Array('template'=>'{input}{label}')); ?>
			<?php echo $form->error($answer,'results'); ?>
		</div>
		
	</div>
	<!-- /левая колоночка -->
	
	<!-- правая колоночка -->
	<div class="rCol"> 
	<div class="f">		
		<div class="bx-yandex-view-layout">
			<div class="bx-yandex-view-map">
			<div id="ymapcontainer" class="ymapcontainer"></div>
			<?php Yii::app()->clientScript->registerScript('initmap',<<<EOD
				var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
				map.enableScrollZoom();
				map.setCenter(new YMaps.GeoPoint({$model->LONGITUDE},{$model->LATITUDE}), 14);
				var placemark = new YMaps.Placemark(new YMaps.GeoPoint({$model->LONGITUDE},{$model->LATITUDE}), { hideIcon: false, hasBalloon: false });
				map.addOverlay(placemark);
EOD
,CClientScript::POS_READY);
?>
			</div>
		</div>
		<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />
	</div>		
		
		
		<!-- камент -->
		<div class="f">
			<?php echo $form->labelEx($answer,'comment'); ?>
			<?php echo $form->textArea($answer,'comment',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($answer,'comment'); ?>
		</div>

	</div>
	<!-- /правая колоночка -->
	<div class="addSubmit">
		<div class="container">
			<div class="btn" onclick="$(this).parents('form').submit();">
				<a class="addFact"><i class="text">Отправить</i><i class="arrow"></i></a>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->