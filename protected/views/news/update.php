<?php
$this->breadcrumbs=array(
	'Новости'=>array('admin'),
	$model->title=>array('view','id'=>$model->id),
	'Редактирование',
);

$this->menu=array(
	array('label'=>'Список новостей', 'url'=>array('admin')),
	array('label'=>'Создание новости', 'url'=>array('create')),
);
?>

<h1>Редактирование новости «<?php echo $model->title; ?>»</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>