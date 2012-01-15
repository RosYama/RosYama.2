<?php
$this->breadcrumbs=array(
	Yii::t('userGroupsModule.recovery','User Activation'),
);
?>
<div id="userGroups-container">
	<?php if(Yii::app()->user->hasFlash('mail')):?>
    <h2>
        <?php echo Yii::app()->user->getFlash('mail'); ?>
    </h2>
	<?php endif; ?>
	<div class="form center">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-passrequest-form',
			'enableAjaxValidation'=>true,
		)); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'username'); ?>
			<?php echo $form->textField($model,'username'); ?>
			<?php echo $form->error($model,'username'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email'); ?>
			<?php echo $form->error($model,'email'); ?>
		</div>
		<?php if (isset($model->errors['answer']) && !isset($model->errors['email']) && !isset($model->errors['username'])): ?>
		<div class="row">
			<h2><?php echo $model->errors['question'][0]; ?></h2>
			<?php echo $form->labelEx($model,'answer'); ?>
			<?php echo $form->textField($model,'answer'); ?>
			<?php echo $form->error($model,'answer'); ?>
		</div>
		<?php endif; ?>
		<div class="row buttons">
			<?php echo CHtml::submitButton(Yii::t('userGroupsModule.general','Request New Password')); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
</div>