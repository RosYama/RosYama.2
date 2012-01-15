<h1><?php echo Yii::t('userGroupsModule.general', 'Groups'); ?></h1>
<?php if(Yii::app()->user->hasFlash('group')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('group'); ?>
    </div>
<?php endif; ?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$groupModel->search(),
	'id'=>'user-groups-group-grid',
	'enableSorting'=>false,
	'filter'=>$groupModel,
	'summaryText'=>false,
	'selectionChanged'=> 'function(id) { getPermission("'.Yii::app()->baseUrl.'", "'.UserGroupsAccess::GROUP.'", $.fn.yiiGridView.getSelection(id))}',
	'columns'=>array(
		'groupname',
		'level',
	),
)); ?>
<?php
if (Yii::app()->user->pbac('userGroups.admin.admin'))
	echo CHtml::ajaxLink(Yii::t('userGroupsModule.admin', 'add group'), 
	Yii::app()->createUrl('/userGroups/admin/accessList', array('what'=>UserGroupsAccess::GROUP, 'id'=>'new')), 
	array('success'=>'js: function(data){ $("#group-detail").slideUp("slow", function(){ $("#group-detail").html(data).slideDown();}); }'),
	array('id'=>'new-group-'.time()));
?>
<div id="group-detail" style="display:none;"></div>