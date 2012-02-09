<h1 class="closed" onclick="$(this).toggleClass('opened').nextAll('.hidden-panel').first().slideToggle();"><?php echo Yii::t('userGroupsModule.admin', 'Main Configurations'); ?></h1>
<?php if(Yii::app()->user->hasFlash('configuration')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('configuration'); ?>
    </div>
<?php endif; ?>
<div class="hidden-panel">
	<?php 
	if (Yii::app()->user->pbac('admin.admin')) {
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-admin-form',
			'enableAjaxValidation'=>false,
		));
	}
	?>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>$confDataProvider,
		'id'=>'configuration-list',
		'enableSorting'=>false,
		'summaryText'=>false,
		'selectableRows'=>0,
		'columns'=>array(
			'rule',
			array(
				'name'=>'render',
				'type'=>'raw',
			),
			array(
				'name'=>'description',
				'type'=>'raw',
			), 
		),
	)); ?>
	<?php if (Yii::app()->user->pbac('admin.admin')): ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>
	<?php $this->endWidget(); ?>
	<?php endif; ?>
</div>