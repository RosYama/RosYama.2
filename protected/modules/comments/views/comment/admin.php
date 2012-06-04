<?php
$this->breadcrumbs=array(
	Yii::t('CommentsModule.msg', 'Comments')=>array('index'),
	Yii::t('CommentsModule.msg', 'Manage'),
);
?>

<h1><?php echo Yii::t('CommentsModule.msg', 'Manage Comments');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'comment-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
                /*array(
                    'name'=>'owner_name',
                    'htmlOptions'=>array('width'=>50),
                ),*/
                array(
                    'name'=>'owner_id',
                    'htmlOptions'=>array('width'=>50),
                    'header'=>'ID ямы'
                ),
                array(
                    'header'=>Yii::t('CommentsModule.msg', 'User Name'),
                    'value'=>'$data->userName',
                    'htmlOptions'=>array('width'=>80),
                ),
                array(
                    'header'=>Yii::t('CommentsModule.msg', 'Link'),
                    'value'=>'CHtml::link(CHtml::link(Yii::t("CommentsModule.msg", "Link"), $data->pageUrl, array("target"=>"_blank")))',
                    'type'=>'raw',
                    'htmlOptions'=>array('width'=>50),
		),
		'comment_text',
                array(
                    'name'=>'create_time',
                    'type'=>'datetime',
                    'htmlOptions'=>array('width'=>70),
                    'filter'=>false,
                ),
		/*'update_time',*/
		array(
                    'name'=>'status',
                    'value'=>'$data->textStatus',
                    'htmlOptions'=>array('width'=>50),
                    'filter'=>Comment::model()->getStatuses(),
                ),
		array(
			'class'=>'CButtonColumn',
                        'deleteButtonImageUrl'=>false,
                        'buttons'=>array(
                            'approve' => array(
                                'label'=>Yii::t('CommentsModule.msg', 'Approve'),
                                'url'=>'Yii::app()->urlManager->createUrl(CommentsModule::APPROVE_ACTION_ROUTE, array("id"=>$data->comment_id))',
                                'options'=>array('style'=>'margin-right: 5px;'),
                                'click'=>'function(){
                                    if(confirm("'.Yii::t('CommentsModule.msg', 'Approve this comment?').'"))
                                    {
                                        $.post($(this).attr("href")).success(function(data){
                                            data = $.parseJSON(data);
                                            if(data["code"] === "success")
                                            {
                                                $.fn.yiiGridView.update("comment-grid");
                                            }
                                        });
                                    }
                                    return false;
                                }',
                            ),
                        ),
                        'template'=>'{approve}{delete}',
                        'header'=>CHtml::dropDownList(
							'pageSize', Yii::app()->user->getState('pageSize',30),
							array(10=>10, 30=>30,50=>50,100=>100,200=>200),
							array('class'=>'change-pagesize')
							)
		),
	),
)); ?>

<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('comment-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY);
