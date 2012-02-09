<?php
$this->breadcrumbs=array(
	'Hole Types',
);

$this->menu=array(
	array('label'=>'Create HoleTypes', 'url'=>array('create')),
	array('label'=>'Manage HoleTypes', 'url'=>array('admin')),
);
?>

<h1>Hole Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
