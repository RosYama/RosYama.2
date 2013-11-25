<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'gibdd-ru-form-_form_gibddru-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_11'); ?>
		<?php echo $form->textField($model,'form_text_11'); ?>
		<?php echo $form->error($model,'form_text_11'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_12'); ?>
		<?php echo $form->textField($model,'form_text_12'); ?>
		<?php echo $form->error($model,'form_text_12'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_checkbox_AGREE'); ?>
		<?php echo $form->textField($model,'form_checkbox_AGREE'); ?>
		<?php echo $form->error($model,'form_checkbox_AGREE'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_dropdown_SUBJECT'); ?>
		<?php echo $form->textField($model,'form_dropdown_SUBJECT'); ?>
		<?php echo $form->error($model,'form_dropdown_SUBJECT'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_13'); ?>
		<?php echo $form->textField($model,'form_text_13'); ?>
		<?php echo $form->error($model,'form_text_13'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_15'); ?>
		<?php echo $form->textField($model,'form_text_15'); ?>
		<?php echo $form->error($model,'form_text_15'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_16'); ?>
		<?php echo $form->textField($model,'form_text_16'); ?>
		<?php echo $form->error($model,'form_text_16'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_17'); ?>
		<?php echo $form->textField($model,'form_text_17'); ?>
		<?php echo $form->error($model,'form_text_17'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_19'); ?>
		<?php echo $form->textField($model,'form_text_19'); ?>
		<?php echo $form->error($model,'form_text_19'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'captcha_word'); ?>
		<?php echo $form->textField($model,'captcha_word'); ?>
		<?php echo $form->error($model,'captcha_word'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_14'); ?>
		<?php echo $form->textField($model,'form_text_14'); ?>
		<?php echo $form->error($model,'form_text_14'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_26'); ?>
		<?php echo $form->textField($model,'form_text_26'); ?>
		<?php echo $form->error($model,'form_text_26'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_27'); ?>
		<?php echo $form->textField($model,'form_text_27'); ?>
		<?php echo $form->error($model,'form_text_27'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'form_text_18'); ?>
		<?php echo $form->textField($model,'form_text_18'); ?>
		<?php echo $form->error($model,'form_text_18'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->