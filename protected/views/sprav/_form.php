<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holes-form',
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
	
		
		<?php echo $form->hiddenField($model,'lat'); ?>
		<?php echo $form->hiddenField($model,'lng'); ?>
		<?php echo $form->hiddenField($model,'str_subject'); ?>
	</div>
	<!-- /левая колоночка -->
	
	<!-- правая колоночка -->
	<div class="rCol"> 
	<div class="f">
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
		
		
		<div class="bx-yandex-view-layout">
			<div class="bx-yandex-view-map">
		<?php if ($model->isNewRecord) $maptype='add'; else $maptype='update'; ?>
		<?php Yii::app()->clientScript->registerScript('initmap',<<<EOD
		if (window.attachEvent) // IE
			window.attachEvent("onload", function(){init_MAP_DzDvWLBsil(null,'{$maptype}')});
		else if (window.addEventListener) // Gecko / W3C
			window.addEventListener('load', function(){init_MAP_DzDvWLBsil(null,'{$maptype}')}, false);
		else
			window.onload = function(){init_MAP_DzDvWLBsil(null,'{$maptype}')};
EOD
,CClientScript::POS_HEAD);
?>
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