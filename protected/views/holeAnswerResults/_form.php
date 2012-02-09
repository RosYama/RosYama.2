<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'hole-answer-results-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> являются обязательными.</p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="<?php if ($model->published) echo "messageSummary"; else echo "errorSummary";?>">
		<?php echo $form->labelEx($model,'published'); ?>
	    <?php echo $form->dropDownList($model,"published",Array(1=>"ДА",0=>"НЕТ")); ?>
		<?php echo $form->error($model,'published'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->