<?php
$this->breadcrumbs=array(
	'Типы ям'=>array('index'),
	'Управление',
);

$this->menu=array(
	array('label'=>'Создать тип ямы', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('hole-types-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление типами ям</h1>

<?php $this->widget('zii.widgets.jui.CJuiSortable', array('items'=>array(), 'options'=>array(),)); ?>
<?php // sortable script
Yii::app()->clientScript->registerScript('sortable', '
function sortablegrid(){
$("#hole-types-grid tbody").sortable({
					  	"delay":"10",
					  	 stop: function(event, ui) {
					  	 thisid=$(ui.item).children("td").children("input.order_id").val();
					  	 beforeid=$(ui.item).next("tr").children("td").children("input.order_id").val();
					  	 afterid=$(ui.item).prev("tr").children("td").children("input.order_id").val();
						 $.fn.yiiGridView.update("hole-types-grid", {
							type:"POST",
							url:"'.$this->createUrl('order').'&id="+thisid+"&dir=movebefore&beforeid="+beforeid+"&afterid="+afterid,
							success:function() {
								$.fn.yiiGridView.update("hole-types-grid");
							}
						});
					  	 }
					  });
//jQuery("#hole-types-grid tbody").disableSelection();
}
sortablegrid();
');


?>

<?php  $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>

<?php echo CHtml::beginForm($this->createUrl("itemsSelected"),'post');
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'hole-types-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'selectableRows'=>2,
	'afterAjaxUpdate'=>'function(id, data){sortablegrid();}',	
	'columns'=>array(
		'id',
			'alias',
			'name',
			/*'pdf_body',
			'pdf_footer',*/
			array(       
            'name'=>'published',
            'type'=>'raw',
            'filter'=>Array(1=>"опубликовано",0=>"неопубликовано"),
            'value'=>'$data->publish',
        ),
		array(
			'name'=>'ordering',
			'type'=>'raw',
			'value'=>'$data->SortOrder',
			),
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
        $.fn.yiiGridView.update('hole-types-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('ajaxupdate', "
$('#hole-types-grid a.ajaxupdate').live('click', function() {
        $.fn.yiiGridView.update('hole-types-grid', {
                type: 'POST',
                url: $(this).attr('href'),
                success: function() {
                        $.fn.yiiGridView.update('hole-types-grid');
                }
        });
        return false;
});
");?>
