<?php
$this->breadcrumbs=array(
	'Holes'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Список ям', 'url'=>array('index')),
	array('label'=>'Добавить яму', 'url'=>array('add')),
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

<h1>Управление ямами</h1>

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
<?php
echo CHtml::beginForm($this->createUrl("itemsSelected"),'post');
$actionbuttons='
<i>С отмеченными:</i>
<button class="mult_submit" name="submit_mult" value="Отмодерировать" title="Отмодерировать">
<img src="/images/b_usrcheck.png" title="Отмодерировать" alt="Отмодерировать" class="icon" width="16" height="16" /></button>
<button class="mult_submit" name="submit_mult" value="Демодерировать" title="Демодерировать">
<img src="/images/b_usrdrop.png" title="Демодерировать" alt="Демодерировать" class="icon" width="16" height="16" /></button>
<button class="mult_submit" name="submit_mult" value="В архив" title="В архив">
<img src="/images/b_archive.png" title="В архив" alt="В архив" class="icon" width="16" height="16" /></button>
<button class="mult_submit" name="submit_mult" value="Вытащить из архива" title="Вытащить из архива">
<img src="/images/b_dearchive.png" title="Вытащить из архива" alt="Вытащить из архива" class="icon" width="16" height="16" /></button>
<button class="mult_submit" name="submit_mult" value="Удалить" title="Удалить" onclick="return confirm(\'Вы уверены, что хотите удалить выбранные элементы?\');" 	>
<img src="/images/b_drop.png" title="Удалить" alt="Удалить" class="icon" width="16" height="16" /></button>

';

?>
<?php 
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'holes-grid',
	'dataProvider'=>$model->searchInAdmin(),
	'filter'=>$model,
	//'ajaxUpdate'=>false,
	'selectableRows'=>2,
	'afterAjaxUpdate'=>"function(id, data) {
        jQuery('#date_created').datepicker({'dateFormat':'dd.mm.yy'});
        jQuery('#date_status').datepicker({'dateFormat':'dd.mm.yy'});
    }",
    'summaryText'=>'<table width="100%"><tr><td style="text-align: left;">'.$actionbuttons.'</td><td style="text-align: right;">Элементы {start}—{end} из {count}.</tr></table>',
	'columns'=>array(
		array(
			'class'=>'CCheckBoxColumn',
			'id'=>'itemsSelected',
			'value'=>'$data->ID',
		),
		array(       
            'name'=>'ADR_SUBJECTRF',
            'value'=>'$data->subject ? $data->subject->name_full : "-"',
        ),        
		'ADR_CITY',		
		array(       
            'name'=>'gibdd_id',
            'value'=>'$data->gibdd ? $data->gibdd->name : "-"',
        ),
		array(       
            'name'=>'username',
            'value'=>'$data->user->username',
        ), 
        array(       
            'name'=>'TYPE_ID',
            'value'=>'$data->type->name',
            'filter'=>CHtml::listData( HoleTypes::model()->findAll(Array('order'=>'ordering')), 'id', 'name' ),
        ),
        array(       
            'name'=>'STATE',
            'value'=>'$data->stateName',
            'filter'=>$model->allstates,
        ),
		array(       
            'name'=>'DATE_CREATED',
            'value'=>'date("d.m.Y H:i", $data->DATE_CREATED)',
            'filter'=> $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                    'model'=>$model, //Model object
                    'language'=>'',
                    'attribute'=>'DATE_CREATED', //attribute name
                    'htmlOptions'=>array('class'=>'input date', 'id'=>'date_created'),
                    'options'=>array(
                        'dateFormat'=> 'dd.mm.yy',
                    )
                ),  true),
        ),  
        
        array(       
            'name'=>'DATE_STATUS',
            'value'=>'date("d.m.Y H:i", $data->DATE_STATUS)',
            'filter'=> $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                    'model'=>$model, //Model object
                    'language'=>'',
                    'attribute'=>'DATE_STATUS', //attribute name
                    'htmlOptions'=>array('class'=>'input date', 'id'=>'date_status'),
                    'options'=>array(
                        'dateFormat'=> 'dd.mm.yy',
                    )
                ),  true),
        ), 
       
		'ADDRESS',
		array(       
            'name'=>'PREMODERATED',
            'type'=>'raw',
            'filter'=>Array(1=>"да",0=>"нет"),
            'value'=>'$data->modering',
        ),
        array(       
            'name'=>'archive',
            'type'=>'boolean',
            'filter'=>Array(1=>"да",0=>"нет"),
            'value'=>'$data->archive',
        ),
        array(       
            'name'=>'deleted',
            'type'=>'boolean',
            'filter'=>Array(1=>"да",0=>"нет"),
        ),
		/*
		array(       
            'name'=>'DATE_SENT',
            'type'=>'date',
        ),   		
		'DATE_STATUS',
		'COMMENT1',
		'COMMENT2',				
		'COMMENT_GIBDD_REPLY',
		'GIBDD_REPLY_RECEIVED',		
		'DATE_SENT_PROSECUTOR',
		'LATITUDE',
		'LONGITUDE',		
		*/
		array(
			'class'=>'CButtonColumn',
			'header'=>CHtml::dropDownList(
                'pageSize', Yii::app()->user->getState('pageSize',20),
                array(10=>10, 20=>20,50=>50,100=>100,200=>200),
                array('class'=>'change-pagesize')
                )
		),
	),
)); 
echo CHtml::endForm();
?>

<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('holes-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('ajaxupdate', "
$('#holes-grid a.ajaxupdate').live('click', function() {
        $.fn.yiiGridView.update('holes-grid', {
                type: 'POST',
                url: $(this).attr('href'),
                success: function() {
                        $.fn.yiiGridView.update('holes-grid');
                }
        });
        return false;
});

$('.mult_submit').live('click', function() {
        $.fn.yiiGridView.update('holes-grid', {
                type: 'POST',
                url: $(this).parents('form').attr('action'),
                data: $(this).parents('form').serialize()+'&submit_mult='+$(this).val(),
                success: function() {
                        $.fn.yiiGridView.update('holes-grid');
                }
        });
        return false;
});
");?>
