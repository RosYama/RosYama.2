<?php
$this->breadcrumbs=array(
	'Menus'=>array('index'),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'Управление меню', 'url'=>array('index')),
);
?>

<h1>Редактирование пункта меню "<?php echo $model->name; ?>"</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>