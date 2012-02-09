<?php
$this->breadcrumbs=array(
	'Новости'=>array('admin'),
	'Список',
);

$this->menu=array(
	array('label'=>'Список новостей', 'url'=>array('admin')),
	array('label'=>'Создание новости', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('news-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление новостями</h1>

<?php $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'news-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
	//	'id',
		array(       
            'name'=>'date',
            'type'=>'date',
        ),   
		'title',

		array(       
            'name'=>'published',
            'type'=>'raw',
            'filter'=>Array(1=>"опубликовано",0=>"неопубликовано"),
            'value'=>'$data->publish',
        ),
        
     
		/*
		'archive',
		'introtext',
		'fulltext',
		*/
		array(
			'class'=>'CButtonColumn',
			'header'=>CHtml::dropDownList(
                'pageSize', $pageSize,
                array(5=>5,20=>20,50=>50,100=>100),
                array('class'=>'change-pagesize')
                )
		),
	),
)); ?>

<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('countries-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>
