<?php
$this->breadcrumbs=array(
	Yii::t('UserGroupsModule.recovery','User Activation'),
);

?>
<div id="userGroups-container">
	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-recovery-form',
			'enableAjaxValidation'=>true,
		)); ?>
		<?php if (strpos($model->username, '_user') === 0): ?>
		<div class="row">
			<?php echo $form->labelEx($model,'username'); ?>
			<?php echo $form->textField($model,'username'); ?>
			<?php echo $form->error($model,'username'); ?>
		</div>
		<?php endif; ?>
		<div>
			<?php echo $form->label($model, 'password') ?>
			<?php echo $form->passwordField($model,'password'); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>
		<div>
			<?php echo $form->label($model, 'password_confirm') ?>
			<?php echo $form->passwordField($model,'password_confirm'); ?>
			<?php echo $form->error($model,'password_confirm'); ?>
		</div>
		<?php if (UserGroupsConfiguration::findRule('simple_password_reset') === false): ?>
		<div>
			<?php echo $form->label($model, 'question') ?>
			<?php echo $form->textField($model,'question'); ?>
			<?php echo $form->error($model,'question'); ?>
		</div>
		<div>
			<?php echo $form->label($model, 'answer') ?>
			<?php echo $form->textField($model,'answer'); ?>
			<?php echo $form->error($model,'answer'); ?>
		</div>
		<?php endif ?>
		<div class="row buttons">
			<?php echo CHtml::submitButton(Yii::t('UserGroupsModule.general','Update')); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
</div>