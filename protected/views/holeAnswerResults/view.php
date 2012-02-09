<?php
$this->breadcrumbs=array(
	'Hole Answer Results'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List HoleAnswerResults', 'url'=>array('index')),
	array('label'=>'Create HoleAnswerResults', 'url'=>array('create')),
	array('label'=>'Update HoleAnswerResults', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete HoleAnswerResults', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage HoleAnswerResults', 'url'=>array('admin')),
);
?>

<h1>View HoleAnswerResults #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'published',
		'ordering',
	),
)); ?>
