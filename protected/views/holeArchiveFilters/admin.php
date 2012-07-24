<?php
$this->breadcrumbs=array(
	'Hole Archive Filters'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Создать правило', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('hole-archive-filters-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Правила автоматической архивации ям</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'hole-archive-filters-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		array(       
            'name'=>'type_id',
            'value'=>'$data->type ? $data->type->name : "-"',
            'filter'=>CHtml::listData( HoleTypes::model()->findAll(Array('order'=>'ordering')), 'id', 'name' ),
        ),
        array(       
            'name'=>'status',
            'value'=>'$data->status ? $data->status : "-"',
            'filter'=>Holes::model()->Allstates,
        ),
        array(       
            'name'=>'time_to',
            'value'=>'$data->time_to ? $data->timeSelector[$data->time_to] : "-"',
            'filter'=>$model->timeSelector,
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
