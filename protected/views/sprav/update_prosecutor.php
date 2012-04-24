<?
$this->pageTitle=Yii::app()->name . ' - Справочник ГИБДД';
$this->title=CHtml::link($model->gibdd_name, Array('local','id'=>$model->id)).' > '.'Редактирование';
?>
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
	<div class="lCol" style="width:100%">

	
		<div class="f">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'name'); ?>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'gibdd_name'); ?>
			<?php echo $form->textField($model,'gibdd_name',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'gibdd_name'); ?>
		</div>
		
		<div class="f">
			<?php echo $form->labelEx($model,'preview_text'); ?>
			<?php echo $form->textArea($model,'preview_text',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'preview_text'); ?>
		</div>		
		
		<div class="f">
			<?php echo $form->labelEx($model,'url_priemnaya'); ?>
			<?php echo $form->textField($model,'url_priemnaya',array('class'=>'textInput')); ?>
			<?php echo $form->error($model,'url_priemnaya'); ?>
			<em class="hint">Пример: http://www.site.ru/page.html</em>
		</div>
		
	</div>
	<!-- /левая колоночка -->
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