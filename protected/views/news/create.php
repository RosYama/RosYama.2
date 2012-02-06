<?php
$this->breadcrumbs=array(
	'Новости'=>array('admin'),
	'Создание новости',
);

$this->menu=array(
	array('label'=>'Список новостей', 'url'=>array('admin')),
	array('label'=>'Создание новости', 'url'=>array('create')),
);
?>

<h1>Создание новости</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>