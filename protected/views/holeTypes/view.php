<?php
$this->breadcrumbs=array(
	'Hole Types'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List HoleTypes', 'url'=>array('index')),
	array('label'=>'Create HoleTypes', 'url'=>array('create')),
	array('label'=>'Update HoleTypes', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete HoleTypes', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage HoleTypes', 'url'=>array('admin')),
);
?>

<h1>View HoleTypes #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'alias',
		'name',
		'pdf_body',
		'pdf_footer',
		'published',
		'ordering',
	),
)); ?>
