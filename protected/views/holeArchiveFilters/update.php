<?php
$this->breadcrumbs=array(
	'Hole Archive Filters'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Список правил архивации ям', 'url'=>array('admin')),
	array('label'=>'Создать правило', 'url'=>array('create')),
);
?>

<h1>Изменить правило "<?php echo $model->name; ?>"</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>