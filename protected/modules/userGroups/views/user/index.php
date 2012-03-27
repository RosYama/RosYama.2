<?php
$this->breadcrumbs=array(
	Yii::t('UserGroupsModule.general','User List'),
);
?>
<div id="userGroups-container">
	<div class="userGroupsMenu-container">
		<?php $this->renderPartial('/admin/menu', array('mode' => 'profile', 'list' => true))?>
	</div>
	<h1>Users List</h1>

	<p>
	You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
	or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
	</p>
	
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'user-groups-user-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'selectableRows'=>0,
		'columns'=>array(
			array(
				'name'=>'username',
				'value'=> Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin') ? 
					'CHtml::link($data->username, Yii::app()->baseUrl ."/userGroups?u=".$data->id)' : '$data->username',
				'type'=>'raw',
			),
			Array(
			'name'=>'group_name',
			'filter'=>CHtml::listData( UserGroupsGroup::model()->findAll(Array('order'=>'level DESC')), 'groupname', 'groupname' ),  
			),
			array(
				'name'=>'email',
				'visible'=>Yii::app()->user->pbac('userGroups.user.admin'),
			),
			array(
				'name'=>'status',
				'value'=>'$data->status !=1 ? UserGroupsLookup::resolve("status",$data->status) : UserGroupsLookup::resolve("status",$data->status)."<br/>".CHtml::link("активировать", Array("/userGroups/user/adminActivate","id"=>$data->id), Array("class"=>"ajaxupdate"))',
				'visible'=>Yii::app()->user->pbac('userGroups.user.admin'),
				'type'=>'raw',
				'filter' => CHtml::dropDownList('UserGroupsUser[status]', $model->status, array_merge(array('null' => Yii::t('UserGroupsModule.admin','all')), CHtml::listData(UserGroupsLookup::model()->findAll(), 'value', 'text')) ),
			),
			/*
			'group_id',
			'password',
			'id',
			'access',
			'salt',
			,
			'activation_code',
			'activation_time',
			'last_login',
			'ban',
			*/
			array(
			'class'=>'CButtonColumn',
			'template'=>'{delete}',
		),
		),
	)); 
	Yii::app()->clientScript->registerScript('ajaxupdate', "
	$('#user-groups-user-grid a.ajaxupdate').live('click', function() {
			$.fn.yiiGridView.update('user-groups-user-grid', {
					type: 'POST',
					url: $(this).attr('href'),
					success: function() {
							$.fn.yiiGridView.update('user-groups-user-grid');
					}
			});
			return false;
	});
");?>
	
</div>