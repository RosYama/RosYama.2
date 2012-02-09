<?php
$this->breadcrumbs=array(
	'Holes'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Holes', 'url'=>array('index')),
	array('label'=>'Create Holes', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('holes-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Holes</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'holes-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'ID',
		'USER_ID',
		'LATITUDE',
		'LONGITUDE',
		'ADDRESS',
		'STATE',
		/*
		'DATE_CREATED',
		'DATE_SENT',
		'DATE_STATUS',
		'COMMENT1',
		'COMMENT2',
		'TYPE',
		'ADR_SUBJECTRF',
		'ADR_CITY',
		'COMMENT_GIBDD_REPLY',
		'GIBDD_REPLY_RECEIVED',
		'PREMODERATED',
		'DATE_SENT_PROSECUTOR',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
