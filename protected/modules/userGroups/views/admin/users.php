<h1><?php echo Yii::t('userGroupsModule.general', 'Users'); ?></h1>
<?php if(Yii::app()->user->hasFlash('user')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('user'); ?>
    </div>
<?php endif; ?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$userModel->search(),
	'id'=>'user-groups-user-grid',
	'enableSorting'=>false,
	'enablePagination'=>false,
	'filter'=>$userModel,
	'summaryText'=>false,
	'selectionChanged'=>'function(id) { getPermission("'.Yii::app()->baseUrl.'", "'.UserGroupsAccess::USER.'", $.fn.yiiGridView.getSelection(id))}',
	'columns'=>array(
		'username',
		array(
			'name'=>'status',
			'value'=>'UserGroupsLookup::resolve("status",$data->status).
				((int)$data->status === UserGroupsUser::WAITING_ACTIVATION || (int)$data->status === UserGroupsUser::PASSWORD_CHANGE_REQUEST 
				? ": <b>".$data->activation_code."</b>" : NULL).
				((int)$data->status === UserGroupsUser::BANNED ? ": <b>".$data->ban."</b>" : NULL)',
			'type'=>'raw',
			'filter' => CHtml::dropDownList('UserGroupsUser[status]', $userModel->status, array_merge(array('null' => Yii::t('userGroupsModule.admin','all')), CHtml::listData(UserGroupsLookup::model()->findAll(), 'value', 'text')) ),
		),
		Array(
		'name'=>'group_name',
		'filter'=>CHtml::listData( UserGroupsGroup::model()->findAll(Array('order'=>'level')), 'id', 'groupname' ),  
		),
	),
)); ?>
<?php
if (Yii::app()->user->pbac('userGroups.admin.admin')) 	
	echo CHtml::ajaxLink(Yii::t('userGroupsModule.admin', 'add user'), 
	Yii::app()->createUrl('/userGroups/admin/accessList', array('what'=>UserGroupsAccess::USER, 'id'=>'new')), 
	array('success'=>'js: function(data){ $("#user-detail").slideUp("slow", function(){ $("#user-detail").html(data).slideDown();}); }'),
	array('id'=>'new-user-'.time()));
	
?>
<div id="user-detail" style="display:none;"></div>