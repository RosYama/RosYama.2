<div id="userGroups-container">
	<div class="userGroupsMenu-container">
		<?php $this->renderPartial('/admin/menu', array(
			'mode' => 'profile',
			'username' => Yii::app()->user->id === $miscModel->id ? NULL : $miscModel->username,
		)); ?>
	</div>

	<h1><?php echo Yii::t('UserGroupsModule.general','Update User').' '.ucfirst($miscModel->username); ?></h1>

	<h2><?php echo Yii::t('UserGroupsModule.general', 'General Info'); ?></h2>
	<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-groups-misc-form',
		'enableAjaxValidation'=>true,
		'enableClientValidation'=>true,
	)); ?>
		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php if (UserGroupsConfiguration::findRule('personal_home') || Yii::app()->user->pbac(array('user.admin', 'admin.admin'))): ?>
		<div class="row">
			<?php echo $form->labelEx($miscModel,'home'); ?>
			<?php
			$home_lists = UserGroupsAccess::homeList();
			array_unshift($home_lists, Yii::t('UserGroupsModule.admin','Group Home: {home}', array('{home}'=>$miscModel->relUserGroupsGroup->home)));
			?>
			<?php echo $form->dropDownList($miscModel,'home', $home_lists); ?>
			<?php echo $form->error($miscModel,'home'); ?>
		</div>
		<?php endif; ?>
		<div class="row">
			<?php echo $form->labelEx($miscModel,'email'); ?>
			<?php echo $form->textField($miscModel,'email',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($miscModel,'email'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::hiddenField('formID', $form->id) ?>
			<?php echo CHtml::ajaxSubmitButton(Yii::t('UserGroupsModule.general','Update User Profile'), Yii::app()->baseUrl . '/userGroups/user/update/id/'.$passModel->id, array('update' => '#userGroups-container'), array('id' => 'submit-mail'.$passModel->id.rand()) ); ?>
		</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->

	<?php
	// load other profiles
	/*
	foreach ($profiles as $p) {
		$view = $p->profileViews();
		$this->renderPartial('//'.str_replace(array('{','}'), NULL, $p->TableName()).'/'.$view[UserGroupsUser::EDIT], array('model' => $p, 'user_id' => $passModel->id));
	}*/
	?>

	<h2><?php echo Yii::t('UserGroupsModule.general', 'Security'); ?></h2>
	<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-groups-password-form',
		'enableAjaxValidation'=>true,
		'enableClientValidation'=>true,
	)); ?>
		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php if (Yii::app()->user->pbac('userGroups.user.admin') && Yii::app()->user->id !== $passModel->id) :?>
			<?php echo $form->hiddenField($passModel,'old_password', array('value'=>'filler'))?>
		<?php else: ?>
		<div class="row">
			<?php echo $form->labelEx($passModel,'old_password'); ?>
			<?php echo $form->passwordField($passModel,'old_password',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'old_password'); ?>
		</div>
		<?php endif; ?>
		<div class="row">
			<?php echo $form->labelEx($passModel,'password'); ?>
			<?php echo $form->passwordField($passModel,'password',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'password'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($passModel,'password_confirm'); ?>
			<?php echo $form->passwordField($passModel,'password_confirm',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'password_confirm'); ?>
		</div>
		<?php if (UserGroupsConfiguration::findRule('simple_password_reset') === false): ?>
		<div class="row">
			<?php echo $form->labelEx($passModel,'question'); ?>
			<?php echo $form->textField($passModel,'question',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'question'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($passModel,'answer'); ?>
			<?php echo $form->passwordField($passModel,'answer',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'answer'); ?>
		</div>
		<?php endif; ?>
		<div class="row buttons">
			<?php echo CHtml::hiddenField('formID', $form->id) ?>
			<?php echo CHtml::ajaxSubmitButton(Yii::t('UserGroupsModule.general','Change Password'), Yii::app()->baseUrl .'/userGroups/user/update/id/'.$passModel->id, array('update' => '#userGroups-container'), array('id' => 'submit-pass'.$passModel->id.rand()) ); ?>
		</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->
</div>