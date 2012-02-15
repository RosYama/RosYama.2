 <div class="view">
	 
	<b><?php echo CHtml::encode($data->getAttributeLabel('group_id')); ?>:</b>
	<?php echo CHtml::encode($data->relUserGroupsGroup->groupname); ?>
	<br />

	<?php if (Yii::app()->user->id === $data->id || Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin')): ?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />
	<?php endif; ?>

	<?php if (Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin') || Yii::app()->user->id === $data->id): ?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode(UserGroupsLookup::resolve('status', $data->status)); ?>
	<?php endif; ?>
	<br />
</div>



<?php /*
// render the profile extensions
foreach ($profiles as $p) {
echo '//'.str_replace(array('{','}'), NULL, $p['model']->tableName()).'/'.$p['view'];
	$this->renderPartial('//profile/'.$p['view'], array('model' => $p['model']));
} */
?>

<?php #form for user approval ?>
<?php if ((Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin')) && (int)$data->status === UserGroupsUser::WAITING_APPROVAL) : ?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-groups-approval-form',
	'enableAjaxValidation'=>false,
	'action'=>Yii::app()->baseUrl.'/userGroups/user/approve',
)); ?>
<div class="row buttons">
	<?php echo CHtml::hiddenField('UserGroupsApprove[id]', $data->id); ?>
	<?php echo CHtml::submitButton(Yii::t('UserGroupsModule.general','Approve Registration'), array('onclick' => 'js: if(confirm("'. Yii::t('UserGroupsModule.admin', 'Do you really want to approve {who} registration?', array('{who}' => $data->username)).'")) {return true;}else{return false;}')); ?>
</div>
<?php $this->endWidget(); ?>
<?php endif; ?>

<?php #form used to ban user ?>
<?php if ((Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin')) && (int)$data->status === UserGroupsUser::ACTIVE && $data->relUserGroupsGroup->level < Yii::app()->user->level) : ?>
<div id="groups-group-container">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-groups-group-form',
	'enableAjaxValidation'=>false,
	//'action'=>Yii::app()->baseUrl.'/userGroups/user/update/id/'.$data->id,
	
)); ?>

	<div class="row">
		<?php echo $form->labelEx($data,'group_id'); ?>
		<?php echo $form->dropDownList($data, 'group_id', CHtml::listData( UserGroupsGroup::model()->findAll(Array('order'=>'level DESC')), 'id', 'groupname' ));?>
		<?php echo $form->error($data,'group_id'); ?>
	</div>

<div class="row buttons">	
	<?php echo CHtml::ajaxSubmitButton('Сохранить', Yii::app()->baseUrl . '/userGroups/user/changeGroup/id/'.$data->id, array('update' => '#userGroups-container'), array('id' => 'submit-mail'.$data->id.rand()) ); ?>
</div>

<?php $this->endWidget(); ?>
</div>
<br/><br/><br/>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-groups-ban-form',
	'enableAjaxValidation'=>false,
	'action'=>Yii::app()->baseUrl.'/userGroups/user/ban',
)); ?>
<div class="row buttons">
	<?php echo CHtml::hiddenField('UserGroupsBan[id]', $data->id); ?>
	<?php echo CHtml::label(Yii::t('UserGroupsModule.admin','Ban Reason'), 'UserGroupsBan_reason'); ?> 
	<?php echo CHtml::textField('UserGroupsBan[reason]'); ?>
	<?php echo CHtml::label(Yii::t('UserGroupsModule.admin','Ban Period'), 'UserGroupsBan_period'); ?>
	<?php 
	$ban_periods = array(
		1 => Yii::t('UserGroupsModule.admin','{n} Day|{n} Days', array(1)), 
		3 => Yii::t('UserGroupsModule.admin','{n} Day|{n} Days', array(3)), 
		5 => Yii::t('UserGroupsModule.admin','{n} Day|{n} Days', array(5)),
		7 => Yii::t('UserGroupsModule.admin','{n} Day|{n} Days', array(7)),
		14 => Yii::t('UserGroupsModule.admin','{n} Week|{n} Weeks', array(2)), 
		28 => Yii::t('UserGroupsModule.admin','{n} Month|{n} Months', array(1)), 
		56 => Yii::t('UserGroupsModule.admin','{n} Month|{n} Months', array(2)),
		84 => Yii::t('UserGroupsModule.admin','{n} Month|{n} Months', array(3)),
		400 => Yii::t('UserGroupsModule.admin','Forever'),
	);
	?> 
	<?php echo CHtml::dropDownList('UserGroupsBan[period]', NULL, $ban_periods); ?>
	<?php echo CHtml::submitButton(Yii::t('UserGroupsModule.general','Ban User'), array('onclick' => 'js: if(confirm("'. Yii::t('UserGroupsModule.admin', 'Do you really want to ban {who}?', array('{who}' => $data->username)).'")) {return true;}else{return false;}')); ?>
</div>
<?php $this->endWidget(); ?>
<?php endif; ?>