<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'hole-types-form',
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
		<?php echo $form->labelEx($model,'alias'); ?>
		<?php echo $form->textField($model,'alias',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'alias'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'dorogimos_id'); $dmosobj=new DorogiMosForm; ?>
		<?php echo $form->dropDownList($model, 'dorogimos_id', CHtml::listData( $dmosobj->categories, 'code', 'problemCategory')); ?>
		<?php echo $form->error($model,'dorogimos_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pdf_body'); ?>
		<p class="hint">шаблон для вставки описания дефекта: {descr}повреждения дорожного полотна{/descr}</p>
		<?php echo $form->textArea($model,'pdf_body',array('rows'=>8, 'cols'=>80)); ?>
		<?php echo $form->error($model,'pdf_body'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pdf_footer'); ?>
		<?php echo $form->textArea($model,'pdf_footer',array('rows'=>8, 'cols'=>80)); ?>
		<?php echo $form->error($model,'pdf_footer'); ?>
	</div>
	
	<?php for ($i=0;$i<5;$i++) : 
	if ($model->commands 
	&& isset ($model->commands[$i])) {
		$command=$model->commands[$i];
	 echo $form->hiddenField($command,"[$i]id"); 
	}
	else {
	$command=new HoleTypePdfListCommands;
	$command->ordering=$i+1;
	}
	?>
	<?php echo $form->hiddenField($command,"[$i]ordering"); ?>	
	<div class="row">
		<?php echo $form->labelEx($command,"[$i]text"); ?>
		<?php echo $form->textArea($command,"[$i]text",array('rows'=>8, 'cols'=>80)); ?>
		<?php echo $form->error($command,"[$i]text"); ?>
	</div>
	<?php endfor; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->