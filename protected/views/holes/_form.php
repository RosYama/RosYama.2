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
		<div class="hiddenfields" <?php if (!$model->TYPE_ID) echo 'style="display:none;"';?> >
		
		<?php if (Yii::app()->user->level > 99) : ?>
		<div class="f">
			<?php echo $form->labelEx($model,'deleted'); ?>
			<?php echo $form->dropDownList($model, 'deleted', Array(0=>'Нет', 1=>'Да')); ?>
			<?php echo $form->error($model,'deleted'); ?>
			<?php if ($model->deleted) : ?>
				Удалил: <?php echo $model->deletor ? CHtml::link($model->deletor->Fullname, Array('profile/view','id'=>$model->deletor->id)) : 'Непонятно кто' ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<!-- тип дефекта -->
		<div class="f">
			<?php echo $form->labelEx($model,'TYPE_ID'); ?>
			<?php echo $form->dropDownList($model, 'TYPE_ID', CHtml::listData( HoleTypes::model()->findAll(Array('condition'=>'published=1', 'order'=>'ordering')), 'id','name')); ?>
			<?php echo $form->error($model,'TYPE_ID'); ?>
		</div>
		

		<!-- адрес -->
		<div class="f">
			<?php echo $form->labelEx($model,'ADDRESS'); ?>
			<?php echo $form->textField($model,'ADDRESS',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'ADDRESS'); ?>
		</div>
		
		<!-- адрес -->
		<div class="f">
		<?php echo $form->labelEx($model,'gibdd_id'); ?>
		<?php echo $form->dropDownList($model, 'gibdd_id', CHtml::listData( $model->territorialGibdd, 'id', 'gibdd_name' ));?>
		<?php echo $form->error($model,'gibdd_id'); ?>
		</div>
		
		<!-- фотки -->
		<div class="f">
			<?php echo $form->labelEx($model,'upploadedPictures'); ?>
			<?php 
				if (!Yii::app()->user->userModel->relProfile->use_multi_upload) 
					$this->widget('CMultiFileUpload',array('accept'=>'gif|jpg|jpeg|png', 'model'=>$model, 'attribute'=>'upploadedPictures', 'htmlOptions'=>array('class'=>'mf'), 'denied'=>Yii::t('mf','Невозможно загрузить этот файл'),'duplicate'=>Yii::t('mf','Файл уже существует'),'remove'=>Yii::t('mf','удалить'),'selected'=>Yii::t('mf','Файлы: $file'),));
				else $this->widget('ext.EAjaxUpload.EAjaxUpload',
					array(
							'id'=>'uploadFile',
							'config'=>array(
								   'action'=>Yii::app()->createUrl('/holes/upload'),
								   'allowedExtensions'=>array("jpg", "jpeg", "png", "gif"),//array("jpg","jpeg","gif","exe","mov" and etc...
								   'sizeLimit'=>10*1024*1024,// maximum file size in bytes
								   'minSizeLimit'=>20,// minimum file size in bytes
								   'multiple'=>true,
								   //'onComplete'=>"js:function(id, fileName, responseJSON){ alert(fileName); }",
								   'messages'=>array(
								                     'typeError'=>"{file} не верный тип файла. Можно загружать только {extensions}.",
								                     'sizeError'=>"{file} слишком большой файл. Максимальный размер {sizeLimit}.",
								                     'minSizeError'=>"{file} слишком маленький файл. Минимальный размер {minSizeLimit}.",
								                     'emptyError'=>"{file} пуст. Выберите другой файл для загрузки",
								                     'onLeave'=>"Файлы загружаются, если вы выйдете сейчас, загрузка будет прервана."
								                   ),
								   //'showMessage'=>"js:function(message){ alert(message); }"
								  )
					)); ?>
		</div>
		
		<!-- камент -->
		
		<div class="f">
			<?php echo $form->labelEx($model,'description_size'); ?>
			<?php echo $form->textArea($model,'description_size',Array('rows'=>5)); ?>
			<?php echo $form->error($model,'description_size'); ?>
		</div>
		<div class="f">
			<?php echo $form->labelEx($model,'description_locality'); ?>
			<?php echo $form->textArea($model,'description_locality',Array('rows'=>8)); ?>
			<?php echo $form->error($model,'description_locality'); ?>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'COMMENT1'); ?>
			<?php echo $form->textArea($model,'COMMENT1',Array('class'=>'big')); ?>
			<?php echo $form->error($model,'COMMENT1'); ?>
		</div>
	
		<?php echo $form->hiddenField($model,'STR_SUBJECTRF'); ?>
		<?php echo $form->hiddenField($model,'ADR_CITY'); ?>
		</div>
		<div id="coord_fields" style="display:none;">
		<div class="f">
			<?php echo $form->labelEx($model,'LONGITUDE'); ?>
			<?php echo $form->textField($model,'LONGITUDE',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'LONGITUDE'); ?>
		</div>
		<div class="f">
			<?php echo $form->labelEx($model,'LATITUDE'); ?>
			<?php echo $form->textField($model,'LATITUDE',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'LATITUDE'); ?>
		</div>
		<div class="addSubmit">
			<div class="container" style="padding:0px;">				
				<div class="btn">
					<a class="addFact set_by_coord"><i class="text">Показать</i><i class="arrow"></i></a>
				</div>
			</div>
		</div>
		</div>
	</div>
	<!-- /левая колоночка -->
	
	<!-- правая колоночка -->
	<div class="rCol"> 
	<div class="f">

		<div class="bx-yandex-search-layout" style="padding-bottom: 0px;">
			<div class="bx-yandex-search-form" style="padding-bottom: 0px;">				
					<p>Введите адрес места для быстрого поиска</p>
					<input type="text" id="address_inp" name="address" class="textInput" value="" style="width: 300px;" />
					<input type="submit" value="Искать" onclick="jsYandexSearch_MAP_DzDvWLBsil.searchByAddress($('#address_inp').val()); return false;" />
					<a style="display:none;" id="clear_result_link" href="#" onclick="clearSerchResults('MAP_DzDvWLBsil', JCBXYandexSearch_arSerachresults); document.getElementById('address_inp').value=''; return false;">Очистить</a>				
			</div>		
			<div class="bx-yandex-search-results" id="results_MAP_DzDvWLBsil"></div>
		</div>	
			
			<p><strong>
Поставьте метку на карте двойным щелчком мыши
<span class="required">*</span></strong><br />
или <a href="#" id="show_fields">введите координаты дефекта</a>

</p>

			<span id="recognized_address_str" title="Субъект РФ и населённый пункт"></span>
			<span id="other_address_str"></span>	
		
		<div class="bx-yandex-view-layout">
			<div class="bx-yandex-view-map">
		<?php if ($model->isNewRecord) $maptype='addhole'; else $maptype='updatehole'; ?>
		<?php Yii::app()->clientScript->registerScript('initmap',<<<EOD
		
		$('#show_fields').live('click',function() {
			$("#coord_fields").animate({opacity: 'show'}, 'slow');				
				if(jQuery.browser.safari){
							jQuery("body").animate( { scrollTop: $("#Holes_LATITUDE").offset().top-20 }, 1100 );
						  }else{
							jQuery("html").animate( { scrollTop: $("#Holes_LATITUDE").offset().top-20 }, 1100 );
						  }			
			$("#Holes_LONGITUDE").focus();
			
		});
		
		if (window.attachEvent) // IE
			window.attachEvent("onload", function(){init_MAP_DzDvWLBsil(null,'{$maptype}')});
		else if (window.addEventListener) // Gecko / W3C
			window.addEventListener('load', function(){init_MAP_DzDvWLBsil(null,'{$maptype}')}, false);
		else
			window.onload = function(){init_MAP_DzDvWLBsil(null,'{$maptype}')};
EOD
,CClientScript::POS_HEAD);
?>

		<input id="MAPLAT" name="MAPLAT" type="hidden" value="" />
		<input id="MAPZOOM" name="MAPZOOM" type="hidden" value="" />
		<input id="Exclude_id" type="hidden" value="<?php echo $model->ID; ?>" />
		<?php
							$this->widget('application.extensions.ymapmultiplot.YMapMultiplot', array(
									'key'=>$this->mapkey,
								   'id' => 'BX_YMAP_MAP_DzDvWLBsil',//id of the <div> container created
								   'label' => 'Тест', //Title for bubble. Used if you are plotting multiple locations of same business
								   'address' =>  Array(), //Array of AR objects
								   'width'=>'100%',
								   'height'=>'400px',						   
								   //'notshow'=>true
							  ));
							?>
			</div>
		</div>
		<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />

	</div>
		<?
		if(!$model->isNewRecord && $model->pictures_fresh && $model->STATE!='fixed' && !$model->GIBDD_REPLY_RECEIVED)
		{
			?>
			<div id="overshadow"><span class="command" onclick="document.getElementById('picts').style.display=document.getElementById('picts').style.display=='block'?'none':'block';">Можно удалить загруженные фотографии</span><div class="picts" id="picts"><?
			foreach($model->pictures_fresh as $i=>$picture)
			{				
				echo '<br>'.$form->checkBox($model,"deletepict[$i]",Array('class'=>'filter_checkbox','value'=>$picture->id)).' ';
				echo $form->labelEx($model,"deletepict[$i]",Array('label'=>'Удалить фотографию?')).'<br><img src="'.$picture->medium.'"><br><br>';
			}
			echo '</div></div>';
		} ?>
		
		<?php if($model->COMMENT2) : ?>
		<!-- камент -->
		<div class="f">
			<?php echo $form->labelEx($model,'COMMENT2'); ?>
			<?php echo $form->textArea($model,'COMMENT2',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($model,'COMMENT2'); ?>
		</div>
		<? endif;  ?>
	</div>
	<!-- /правая колоночка -->
	<div class="hiddenfields" <?php if (!$model->TYPE_ID) echo 'style="display:none;"';?> >
	
		<div class="addSubmit">
			<div class="container">
				<p>После нажатия на кнопку «Отправить» вы можете создать обращение о дефекте в виде pdf-документа, которое можно распечатать и отправить в ближайшее отделение ГИБДД</p>
				<div class="btn" onclick="$(this).parents('form').submit();">
					<a class="addFact"><i class="text">Отправить</i><i class="arrow"></i></a>
				</div>
			</div>
		</div>
	</div>	
<?php $this->endWidget(); ?>

</div><!-- form -->
