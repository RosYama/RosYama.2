<?php
$this->breadcrumbs=array(
	'Menus'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Menu', 'url'=>array('index')),
	array('label'=>'Create Menu', 'url'=>array('create')),
	array('label'=>'Update Menu', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Menu', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Menu', 'url'=>array('admin')),
);
?>

<h1>View Menu #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'type',
		'link',
		'order',
		'parent',
		'name',
	),
)); ?>
