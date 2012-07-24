<?php
$this->breadcrumbs=array(
	'Hole Archive Filters',
);

$this->menu=array(
	array('label'=>'Create HoleArchiveFilters', 'url'=>array('create')),
	array('label'=>'Manage HoleArchiveFilters', 'url'=>array('admin')),
);
?>

<h1>Hole Archive Filters</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
