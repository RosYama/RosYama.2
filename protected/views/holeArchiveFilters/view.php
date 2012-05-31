<?php
$this->breadcrumbs=array(
	'Hole Archive Filters'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List HoleArchiveFilters', 'url'=>array('index')),
	array('label'=>'Create HoleArchiveFilters', 'url'=>array('create')),
	array('label'=>'Update HoleArchiveFilters', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete HoleArchiveFilters', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage HoleArchiveFilters', 'url'=>array('admin')),
);
?>

<h1>View HoleArchiveFilters #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'type_id',
		'status',
		'time_to',
	),
)); ?>
