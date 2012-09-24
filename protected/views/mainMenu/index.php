<?php
$this->breadcrumbs=array(
	'Menus',
);

?>
<h1>Управление Меню</h1>

<?php $pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'menu-grid',
	'dataProvider'=>$model->search(),
	'columns'=>array(
		'id',
		'nameExt',
		array(            // display 'create_time' using an expression
            'name'=>'type',
            'header'=>'Тип',
            'value'=>'$data->typestring',
        ),
        array(            // display 'create_time' using an expression
            'name'=>'controller',
            'value'=>'$data->controllerstring',
        ),
        array(            // display 'create_time' using an expression
            'name'=>'action',
            'value'=>'$data->actionstring',
        ),

		array(
			'class'=>'CButtonColumn',

            'htmlOptions'=>Array('width'=>'15%'),
			'buttons'=>Array(
					    'up' => array(
				            'label'=>'Переместить вверх',
				            'imageUrl'=>'/images/uparrow.png',
				            'url'=>'Yii::app()->createUrl("menu/up", array("id"=>$data->id))',
				            'click'=>'function() {$.fn.yiiGridView.update("menu-grid", {
										type:"POST",
										url:$(this).attr("href"),
										success:function() {
											$.fn.yiiGridView.update("menu-grid");
										}
									});
									return false; }',
				        ),
						'down' => array(
						    'label'=>'Переместить вниз',     // text label of the button
						    'url'=>'Yii::app()->createUrl("menu/down", array("id"=>$data->id))',      // a PHP expression for generating the URL of the button
						    'imageUrl'=>'/images/downarrow.png',  // image URL of the button. If not set or false, a text link is used
						    //'options'=>array(), // HTML options for the button tag
						    'click'=>'function() {$.fn.yiiGridView.update("menu-grid", {
										type:"POST",
										url:$(this).attr("href"),
										success:function() {
											$.fn.yiiGridView.update("menu-grid");
										}
									});
									return false; }',     // a JS function to be invoked when the button is clicked
						    //'visible'=>'',   // a PHP expression for determining whether the button is visible
						)

			),
			'template'=>'{up}{down}{update}{delete}',
			'header'=>CHtml::dropDownList(
                'pageSize', $pageSize,
                array(5=>5,20=>20,50=>50,100=>100),
				//array('onchange'=>"$.fn.yiiGridView.update(&#O39;mdl--std--student-details-grid&#O39;,{ data:{pageSize: $(this).val() }})", "name"=>"pageSize", "id"=>"pageSize"),
                array('class'=>'change-pagesize')
                )
		),
	),
)); ?>
<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('menu-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>

<?php echo CHtml::beginForm(); ?>
<?php echo CHtml::hiddenField('tree','manage'); ?>
<?php echo CHtml::listBox('node','1',$data,array('size'=>'10')); ?>
<br /><br />
<?php echo CHtml::textField('name'); ?>
<?php echo CHtml::submitButton('Добавить пункт',array('name'=>'add')); ?><br /><br />


<br /><br />
<?php echo CHtml::dropDownList('nodeto','1',$data); ?>
<br />
<?php echo CHtml::submitButton('Переместить перед выбранным пунктом',array('name'=>'before')); ?>
<?php echo CHtml::submitButton('Переместить в подменю выбраного пункта',array('name'=>'below')); ?>
<?php echo CHtml::endForm(); ?>
