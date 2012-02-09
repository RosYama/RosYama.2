<?php
$this->breadcrumbs=array(
	'Типы ям'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Изменить',
);

$this->menu=array(
	array('label'=>'Список типов ям', 'url'=>array('index')),
	array('label'=>'Создать тип ямы', 'url'=>array('create')),
);
?>

<h1>Изменить тип ямы "<?php echo $model->name; ?>"</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>