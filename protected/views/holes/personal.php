<?
$this->pageTitle=Yii::app()->name . ' :: Мои ямы';
?>
<?php Yii::app()->clientScript->registerScript('select_holes','			
			function selectHoles(arr,del){
				 jQuery.ajax({"type":"POST","beforeSend":function(){
					$("#holes_select_list").empty();
					$("#holes_select_list").addClass("loading");
		
				 },
				 "complete":function(){
						$("#holes_select_list").removeClass("loading");
					},"url":"'.CController::createUrl("selectHoles").'?del="+del,"cache":false,"data":"holes="+arr,
				"success":function(html){
					jQuery("#holes_select_list").html(html);
					}
				});				
			}						
			',
			CClientScript::POS_HEAD);
			?>
<?php Yii::app()->clientScript->registerScript('check_holes','

			checkInList();	
			
			 var scroller = new StickyScroller("#holes_select_list",
			{
            start: 270,
            end: 200000,
            interval: 300,
            range: 100,
            margin: 50
			});
			
			scroller.onNewIndex(function(index)
			{
				$("#scrollbox").html("Index " + index);
			});
					
			var opacity = .25;
			var fadeTime = 500;
			var current;				
			
			 scroller.onScroll(function(index)
				{                        
					//alert(index);
				});
			',
			CClientScript::POS_READY);
			?>				
<div class="lCol">
	
	<div id="holes_select_list">
	<?php 
	$selected=$user->getState('selectedHoles', Array());
	if ($selected || $user->userModel->selected_holes_lists) : ?>
		<?php
		$this->renderPartial('_selected', Array('gibdds'=>$selected ? GibddHeads::model()->with('holes')->findAll('holes.id IN ('.implode(',',$selected).')') : Array(),'user'=>$user->userModel));
		?>
	<?php endif;  ?>
	</div>

</div>

<div class="rCol">
<div class="pdf_form" id="pdf_form" style="display: none; left:auto;">
				<a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
				<div id="gibdd_form"></div>
				</div>
			<p>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	//'method'=>'get',
	'id'=>'holes_selectors',
)); ?>			


<div class="filterCol filterStatus">
<p class="title">Показать дефекты со статусом</p>
<?php foreach ($model->allstatesMany as $alias=>$name) : ?>
	<label><span class="<?php echo $alias; ?>"><?php echo $form->checkBox($model,"states[$alias]",Array('value'=>$alias)); ?></span><ins><?php echo $name; ?></ins></label>
<?php endforeach; ?>	
</div>
<div class="filterCol filterType">
<p class="title">Показать тип дефектов</p>
<?php foreach (HoleTypes::model()->findAll(Array('condition'=>'published=1', 'order'=>'ordering')) as $i=>$type) : ?>
<label class="col2"><span><?php echo $form->checkBox($model,"types[$i]",Array('value'=>$type->id)); ?></span><ins class="<?php echo $type->alias; ?>"><?php echo $type->name; ?></ins></label>
<?php endforeach; ?>
<div class="clear"></div>
<br />
<div class="map_bottom_check">
	<div class="chekboxes withAnswers">
	<?php echo $form->checkBox($model,"withAnswers",Array('class'=>'filter_checkbox')); ?>	
	<?php echo $form->labelEx($model,'withAnswers',Array('label'=>'с загруженными ответами ГИБДД')); ?>		
	</div>

</div>

</div>		
<div class="clear"></div>	
			<?php echo $form->dropDownList($model, 'showUserHoles', Array(1=>'Мои ямы', 2=>'Чужие, на которые я отправил заявление')); ?>
			
			<?php if ($model->keys) echo $form->dropDownList($model, 'gibdd_id', CHtml::listData(GibddHeads::model()->with(Array('holes'=>Array('select'=>'ID, gibdd_id')))->findAll(Array('condition'=>'holes.ID IN ('.implode(', ',$model->keys).')','order'=>'t.name')), 'id', 'gibdd_name' ), array('prompt'=>'Все ГИБДД')); ?>
			
			<?php echo CHtml::submitButton('Найти'); ?><br/>
			<div class="clear"></div><br />
					
						<div style="text-align:right;">
			<?php echo CHtml::checkBox('selectAll', false, Array('id'=>'selectAll','class'=>'state_check')); ?><?php echo CHtml::label('Выбрать все', 'selectAll'); ?>
			</div>
	<?php $this->endWidget(); ?>		
			</p>
				
<?php $this->widget('zii.widgets.CListView', array(
	'id'=>'holes_list',
	'ajaxUpdate'=>true,
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'itemsTagName'=>'ul',
	'cssFile'=>'/css/holes_list.css',
	'itemsCssClass'=>'holes_list',
	'summaryText'=>false,
	'viewData'=>Array('showcheckbox'=>true, 'user'=>$user),
	'afterAjaxUpdate'=> 'function(id){
		checkInList();
		noimageMap();
		}',
	
)); ?>


<?php /* foreach ($model->AllstatesMany as $state_alias=>$state_name) : ?>
<?php  if ($holes[$state_alias]) : ?>
<div class="state_block">
	<h2><?php echo $state_name; ?> <?php echo CHtml::checkBox('state', false, Array('id'=>'state_'.$state_alias,'class'=>'state_check')); ?><?php echo CHtml::label('Выбрать все', 'state_'.$state_alias); ?></h2>
	<ul class="holes_list">
	<?php foreach($holes[$state_alias] as $i=>$item) : ?>
		<?php 
		$this->renderPartial('_view',array(
			'data'=>$item,
			'index'=>$i,
			'showcheckbox'=>true,
		));?>
	<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
<?php endforeach; */ ?>
</div>


