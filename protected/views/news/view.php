<?
$this->pageTitle=Yii::app()->name . ' :: Новости :: '.$model->title;
?>
<?php
$this->breadcrumbs=array(
	'News'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List News', 'url'=>array('index')),
	array('label'=>'Create News', 'url'=>array('create')),
	array('label'=>'Update News', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete News', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage News', 'url'=>array('admin')),
);
?>
<div class="lCol">
<?php $this->widget('application.widgets.social.socialWidget'); ?>
</div>
<div class="rCol">
	<div class="news-detail">
				<h1><?php echo $model->title; ?></h1>
						 <p class="date"><?php echo CHtml::encode(Y::dateFromTime($model->date)); ?></p>
			
	<?php echo $model->fulltext; ?>
	<div style="clear:both"></div>
		<br />
				</div>
	<p><?php echo CHtml::link('Возврат к списку', array('index')); ?></p>
	 
	</div>
</div>
