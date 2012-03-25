<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'ID'); ?>
		<?php echo $form->textField($model,'ID',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'USER_ID'); ?>
		<?php echo $form->textField($model,'USER_ID',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'LATITUDE'); ?>
		<?php echo $form->textField($model,'LATITUDE'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'LONGITUDE'); ?>
		<?php echo $form->textField($model,'LONGITUDE'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ADDRESS'); ?>
		<?php echo $form->textArea($model,'ADDRESS',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'STATE'); ?>
		<?php echo $form->textField($model,'STATE',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DATE_CREATED'); ?>
		<?php echo $form->textField($model,'DATE_CREATED',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DATE_SENT'); ?>
		<?php echo $form->textField($model,'DATE_SENT',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DATE_STATUS'); ?>
		<?php echo $form->textField($model,'DATE_STATUS',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'COMMENT1'); ?>
		<?php echo $form->textArea($model,'COMMENT1',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'COMMENT2'); ?>
		<?php echo $form->textArea($model,'COMMENT2',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'TYPE_ID'); ?>
		<?php echo $form->textField($model,'TYPE_ID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ADR_SUBJECTRF'); ?>
		<?php echo $form->textField($model,'ADR_SUBJECTRF',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'gibdd_id'); ?>
		<?php echo $form->textField($model,'gibdd_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ADR_CITY'); ?>
		<?php echo $form->textField($model,'ADR_CITY',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'COMMENT_GIBDD_REPLY'); ?>
		<?php echo $form->textArea($model,'COMMENT_GIBDD_REPLY',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'GIBDD_REPLY_RECEIVED'); ?>
		<?php echo $form->textField($model,'GIBDD_REPLY_RECEIVED'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'PREMODERATED'); ?>
		<?php echo $form->textField($model,'PREMODERATED'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'DATE_SENT_PROSECUTOR'); ?>
		<?php echo $form->textField($model,'DATE_SENT_PROSECUTOR',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->