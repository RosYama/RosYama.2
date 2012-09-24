<?php
$this->breadcrumbs=array(
	'Menus'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Управление меню', 'url'=>array('index')),
);
?>

<h1>Create Menu</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>