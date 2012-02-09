<?php $name = (int)$what === UserGroupsAccess::USER ? $data->username : $data->groupname; // assign to name it's value ?>
<h3>
	<?php 
	if ((int)$what === UserGroupsAccess::USER) {
		if ($id === 'new')
			echo Yii::t('userGroupsModule.admin', 'New User: Data and Access Permissions');
		else
			echo Yii::t('userGroupsModule.admin', 'User {username}: Data and Access Permissions', array('{username}' => ucfirst($name)));
	} else {
		if ($id === 'new')
			echo Yii::t('userGroupsModule.admin', 'New Group: Data and Access Permissions');
		else
			echo Yii::t('userGroupsModule.admin', 'Group {groupname}: Data and Access Permissions', array('{groupname}' => ucfirst($name)));
	}
	?>
</h3>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-groups-access-form-' . $what,
	'enableAjaxValidation'=>false,
	'action'=> Yii::app()->baseUrl .'/userGroups/admin',
)); ?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'ajaxUpdate'=>false,
	'enableSorting'=>false,
	'summaryText'=>false,
	'id'=>'rule-list',
	'selectableRows'=>0,
	'columns'=>array(
		
		array(
			'name'=>'Module',
		),
		
		array(
			'name'=>'Controller',
		),
		
		array(
			'name'=>'Read',
			'type'=>'raw',
		),
		
		array(
			'name'=>'Write',
			'type'=>'raw',
		),
		
		array(
			'name'=>'Admin',
			'type'=>'raw',
		),
		
	),
));
?>
<?php 
if (Yii::app()->user->pbac('userGroups.admin.admin')) { ?>
	<div class="row">
		<?php 
		if ((int)$what === UserGroupsAccess::GROUP) {
			echo CHtml::label(Yii::t('userGroupsModule.general','Group Level'), 'UserGroupsAccess_'.$what.'_level', array('class'=>'inline')) . CHtml::dropDownList('UserGroupsAccess['.$what.'][level]', $data->level, array_reverse(range(0,Yii::app()->user->level - 1), true));
			echo CHtml::label(Yii::t('userGroupsModule.general','Home'), 'UserGroupsAccess_'.$what.'_home', array('class'=>'inline')) . CHtml::dropDownList('UserGroupsAccess['.$what.'][home]', $data->home, UserGroupsAccess::homeList());
			echo CHtml::label(Yii::t('userGroupsModule.general','Group Name'), 'UserGroupsAccess_'.$what.'_groupname', array('class'=>'inline'));
			echo CHtml::textField('UserGroupsAccess['.$what.'][groupname]', $name);
		}
		if ((int)$what === UserGroupsAccess::USER) {
			echo CHtml::label(Yii::t('userGroupsModule.general','User Name'), 'UserGroupsAccess_'.$what.'_username', array('class'=>'inline'));
			echo CHtml::textField('UserGroupsAccess['.$what.'][username]', $name);
			echo CHtml::label(Yii::t('userGroupsModule.general','Group'), 'UserGroupsAccess_'.$what.'_group_id', array('class'=>'inline')) . CHtml::dropDownList('UserGroupsAccess['.$what.'][group_id]', $data->group_id, UserGroupsGroup::groupList());
			$home_lists = UserGroupsAccess::homeList(); 
			array_unshift($home_lists, Yii::t('userGroupsModule.admin','Group Home: {home}', array('{home}'=>$data->group_home)));
			echo CHtml::label(Yii::t('userGroupsModule.general','Home'), 'UserGroupsAccess_'.$what.'_home', array('class'=>'inline')) . CHtml::dropDownList('UserGroupsAccess['.$what.'][home]', $data->home, $home_lists); 
			echo CHtml::label(Yii::t('userGroupsModule.general','Email'), 'UserGroupsAccess_'.$what.'_email', array('class'=>'inline')) . CHtml::textField('UserGroupsAccess['.$what.'][email]', $data->email);
		}
		?>
	</div>
	<?php if ($id === 'new' && (int)$what === UserGroupsAccess::USER) { ?>
	<div class="row">
		<?php echo CHtml::label(Yii::t('userGroupsModule.general','Password'), 'UserGroupsAccess_'.$what.'_password', array('class'=>'inline')); ?>
		<?php echo CHtml::textField('UserGroupsAccess['.$what.'][password]', $data->password); ?>
	</div>
	<div class="row">
		<p><?php echo Yii::t('userGroupsModule.admin','if you omit the password a random one will be generated'); ?></p>
		<p><?php echo Yii::t('userGroupsModule.admin','if you omit the username the user will be able to chose one upon activation of the account'); ?></p>
	</div>
	<?php } ?>
<?php } ?>

<div class="row buttons left-floated">
	<?php echo CHtml::hiddenField('UserGroupsAccess[what]', $what); ?>
	<?php echo CHtml::hiddenField('UserGroupsAccess[id]', $id); ?>
	<?php echo CHtml::hiddenField('UserGroupsAccess[displayname]', ucfirst($name)); ?>
	<?php echo CHtml::submitButton(Yii::t('userGroupsModule.general', 'Save')); ?>
</div>
<?php $this->endWidget(); ?>
<?php if ($id !== 'new' && Yii::app()->user->pbac('userGroups.admin.admin')): ?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-groups-delete-form-' . $what,
	'enableAjaxValidation'=>false,
	'action'=> Yii::app()->baseUrl .'/userGroups/admin',
)); ?>
<div class="row buttons right-floated">
	<?php echo CHtml::hiddenField('UserGroupsAccess[what]', $what); ?>
	<?php echo CHtml::hiddenField('UserGroupsAccess[id]', $id); ?>
	<?php echo CHtml::hiddenField('UserGroupsAccess[displayname]', ucfirst($name)); ?>
	<?php echo CHtml::hiddenField('UserGroupsAccess[delete]', 'yes'); ?>
	<?php 
	if ((int)$what === UserGroupsAccess::USER)
		$confirm_message = Yii::t('userGroupsModule.admin', 'Do you really want to delete the user {user}?', array('{user}' => ucfirst($name)));
	else {
		$confirm_message = Yii::t('userGroupsModule.admin', 'Do you really want to delete the group {group}?', array('{group}' => ucfirst($name)));
		$confirm_message .= '\n'. Yii::t('userGroupsModule.admin', 'Remember if you delete a Group you\'ll delete all the users that belongs to it.');
	}
		
	?>
	<?php echo CHtml::submitButton(Yii::t('userGroupsModule.general','Delete'), array('onclick' => 'js: if(confirm("'.$confirm_message.'")) {return true;}else{return false;}')); ?>
</div>
<?php $this->endWidget(); ?>
<?php endif; ?>
