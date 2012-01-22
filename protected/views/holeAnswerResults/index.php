<?php
$this->breadcrumbs=array(
	'Результаты запроса в ГИБДД'=>array('index'),
	'Управление',
);

$this->menu=array(
	array('label'=>'Создать новый', 'url'=>array('create')),
);

?>

<h1>Управление результатами запроса в ГИБДД</h1>

<?php   $this->widget('zii.widgets.jui.CJuiSortable', array('items'=>array(), 'options'=>array(),)); ?>
<?php // sortable script
Yii::app()->clientScript->registerScript('sortable', '
function sortablegrid(){
$("#hole-answer-results-grid tbody").sortable({
					  	"delay":"10",
					  	 stop: function(event, ui) {
					  	 thisid=$(ui.item).children("td").children("input.order_id").val();
					  	 beforeid=$(ui.item).next("tr").children("td").children("input.order_id").val();
					  	 afterid=$(ui.item).prev("tr").children("td").children("input.order_id").val();
						 $.fn.yiiGridView.update("hole-answer-results-grid", {
							type:"POST",
							url:"'.$this->createUrl('order').'&id="+thisid+"&dir=movebefore&beforeid="+beforeid+"&afterid="+afterid,
							success:function() {
								$.fn.yiiGridView.update("hole-answer-results-grid");
							}
						});
					  	 }
					  });
//jQuery("#hole-answer-results-grid tbody").disableSelection();
}
sortablegrid();
');


?>

<?php  $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'hole-answer-results-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'selectableRows'=>2,
	'afterAjaxUpdate'=>'function(id, data){sortablegrid();}',
	'summaryText'=>'Элементы {start}—{end} из {count}',
	'columns'=>array(
		'id',
			'name',
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
        $.fn.yiiGridView.update('hole-answer-results-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('ajaxupdate', "
$('#hole-answer-results-grid a.ajaxupdate').live('click', function() {
        $.fn.yiiGridView.update('hole-answer-results-grid', {
                type: 'POST',
                url: $(this).attr('href'),
                success: function() {
                        $.fn.yiiGridView.update('hole-answer-results-grid');
                }
        });
        return false;
});
");?>
