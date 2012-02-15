<?php
$this->breadcrumbs=array(
	Yii::t('UserGroupsModule.general','User Invitation'),
);
?>
<div id="userGroups-container">
	<div class="userGroupsMenu-container">
		<?php $this->renderPartial('/admin/menu', array('mode' => 'profile', 'invite' => true))?>
	</div>
	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-passrequest-form',
			'enableAjaxValidation'=>true,
		)); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email'); ?>
			<?php echo $form->error($model,'email'); ?>
		</div>
		<div class="row buttons">
			<?php echo CHtml::submitButton(Yii::t('UserGroupsModule.general','Invite User')); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
</div>