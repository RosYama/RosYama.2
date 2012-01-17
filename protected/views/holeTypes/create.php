<?php
$this->breadcrumbs=array(
	'Типы ям'=>array('index'),
	'Создать',
);

$this->menu=array(
	array('label'=>'Список типов ям', 'url'=>array('index')),
);
?>

<h1>Создать тип ямы</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>