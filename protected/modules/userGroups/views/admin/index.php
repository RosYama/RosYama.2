<?php
$this->breadcrumbs=array(
	Yii::app()->user->name.' '.Yii::t('userGroupsModule.general','profile')=>array('/userGroups'),
	Yii::t('userGroupsModule.general','Root Tools'),
);
?>
<div id="userGroups-container">
	<?php if ((int)Yii::app()->user->id === UserGroupsUser::ROOT && UserGroupsConfiguration::findRule('version') < UserGroupsInstallation::VERSION): ?>
	<div class="info">
		<?php echo CHtml::link(Yii::t('userGroupsModule.admin','click here update userGroups'), array('admin/update')); ?>
	</div>
	<?php endif; ?>
	<div class="userGroupsMenu-container">
		<?php $this->renderPartial('/admin/menu', array('mode' => 'profile', 'root' => true))?>
	</div>
	<?php if (!UserGroupsConfiguration::findRule('dumb_admin') || Yii::app()->user->pbac('admin')): ?>
	<?php $this->renderPartial('configurations', array('confDataProvider'=>$confDataProvider))?>
	<hr/>
	<?php $this->renderPartial('crons', array('cronDataProvider'=>$cronDataProvider))?>
	<hr/>
	<?php endif; ?>
	<?php $this->renderPartial('groups', array('groupModel'=>$groupModel))?>
	<hr/>
	<?php $this->renderPartial('users', array('userModel'=>$userModel))?>
</div>