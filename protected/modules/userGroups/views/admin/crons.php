<h1 class="closed" onclick="$(this).toggleClass('opened').next('.hidden-panel').slideToggle();"><?php echo Yii::t('userGroupsModule.admin', 'Cron Jobs'); ?></h1>
<?php if(Yii::app()->user->hasFlash('crons')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('crons'); ?>
    </div>
<?php endif; ?>
<div class="hidden-panel">
	<?php 
	if (Yii::app()->user->pbac('userGroups.admin.admin'))
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-cron-form',
			'enableAjaxValidation'=>false,
		)); 
	?>
	<?php
	// load the cronjobs
	UGCron::init();
	UGCron::add(new UGCJGarbageCollection);
	UGCron::add(new UGCJUnban);
	foreach (Yii::app()->controller->module->crons as $c) {
		UGCron::add(new $c);
	} 
	?>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>$cronDataProvider,
		'id'=>'configuration-list',
		'enableSorting'=>false,
		'summaryText'=>false,
		'selectableRows'=>0,
		'columns'=>array(
			'name',
			array(
				'name'=>'lapse',
				'value'=> Yii::app()->user->pbac('userGroups.admin.admin') ? 'CHtml::textField("UserGroupsCron[$data->id]", $data->lapse, array("class" => "lapse"))' : '$data->lapse',
				'type'=>'raw',
			),
			array(
				'name'=>'last_occurrence',
				// TODO when stop supporting php 5.2 use strstr
				'value'=>'substr($data->last_occurrence,0,strpos($data->last_occurrence," "))',
			),
			array(
				'name'=>'status',
				'value'=>'UGCron::getStatus($data->name, true, true)',
				'type'=>'raw',
			), 
			array(
				'name'=>'description',
				'value'=>'UGCron::getDescriptions($data->name, true);',
			),
		),
	)); ?>
	<?php if (Yii::app()->user->pbac('userGroups.admin.admin')): ?>
	<div class="inline buttons">
		<?php echo CHtml::submitButton(Yii::t('userGroupsModule.general','Save')); ?>
	</div>
	<?php $this->endWidget(); ?>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-groups-cron-delete-form-',
		'enableAjaxValidation'=>false,
		'action'=>'/userGroups/admin',
	)); ?>
	<div class="inline buttons">
		<?php echo CHtml::hiddenField('UserGroupsCronRemove[remove]', 'yes'); ?>
		<?php echo CHtml::submitButton(Yii::t('userGroupsModule.admin','Remove not installed CronJobs'), array('onclick' => 'js: if(confirm("'. Yii::t('userGroupsModule.admin', 'Do you really want to remove those CronJobs?').'")) {return true;}else{return false;}')); ?>
	</div>
	<?php $this->endWidget(); ?>
	<?php endif; ?>
</div>