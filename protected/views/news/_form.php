<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'news-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>Array ('enctype'=>'multipart/form-data'),
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> являются обязательными.</p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="<?php if ($model->published) echo "messageSummary"; else echo "errorSummary";?>">
		<?php echo $form->labelEx($model,'published'); ?>
	    <?php echo $form->dropDownList($model,"published",Array(1=>"ДА",0=>"НЕТ")); ?>
		<?php echo $form->error($model,'published'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model'=>$model,
			'value'=>$model->DateValue,
		    'name'=>'News[date]',
		    // additional javascript options for the date picker plugin
		    'options'=>array(
		        'showAnim'=>'fold',
		        'dateFormat'=>'dd.mm.yy',
		    ),
		    'htmlOptions'=>array(
		        'style'=>'height:20px;'
		    ),
		));
		?>
		<?php echo $form->error($model,'date'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'picture'); ?>
	<?php if ($model->picture) : ?>
	<img src="<?php echo $model->Img; ?>" />
	<?php endif; ?>
	<?php echo $form->fileField($model, 'image');?>
	<?php echo $form->error($model,'picture'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'introtext'); ?>
		<?php echo $form->textArea($model,'introtext',array('rows'=>7,'cols'=>90)); ?>
		<?php echo $form->error($model,'introtext'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fulltext'); ?>
		<?php $this->widget('application.extensions.ckeditor.CKEditor', array(
  		"model"=>$model,  "attribute"=>"fulltext",	"language"=>'ru', 	"editorTemplate"=>'full',  ) );?>
		<?php echo $form->error($model,'fulltext'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->