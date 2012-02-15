<?php
$this->breadcrumbs=array(
	Yii::t('UserGroupsModule.recovery','User Activation'),
);

?>
<div id="userGroups-container">
	<?php if(Yii::app()->user->hasFlash('mail')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('mail'); ?>
    </div>
	<?php endif; ?>
	<div class="form center">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-activate-form',
			'enableAjaxValidation'=>false,
		)); ?>
		<div class="row">
			<?php echo $form->labelEx($activeModel,'username'); ?>
			<?php echo $form->textField($activeModel,'username'); ?>
			<?php echo $form->error($activeModel,'username'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($activeModel,'activation_code'); ?>
			<?php echo $form->textField($activeModel,'activation_code'); ?>
			<?php echo $form->error($activeModel,'activation_code'); ?>
		</div>
		<div class="row buttons">
			<?php echo CHtml::hiddenField('id',$form->id); ?>
			<?php echo CHtml::submitButton(Yii::t('UserGroupsModule.general','Proceed')); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
	<div class="form center">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-request-form',
			'enableAjaxValidation'=>false,
		)); ?>
		<div class="row">
			<?php echo $form->labelEx($requestModel,'email'); ?>
			<?php echo $form->textField($requestModel,'email'); ?>
			<?php echo $form->error($requestModel,'email'); ?>
		</div>
		<div class="row buttons">
			<?php echo CHtml::hiddenField('id',$form->id); ?>
			<?php echo CHtml::submitButton(Yii::t('UserGroupsModule.general','Resend Email')); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
</div>