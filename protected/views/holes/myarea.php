<?
$this->pageTitle=Yii::app()->name . ' :: Мой участок';
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
			
			var ofset=$("#area_neighbors").offset().top+$("#area_neighbors").height();
			
			//$("#holes_select_list").offset({ top: ofset})
			
			
				
			',
			CClientScript::POS_READY);
			?>		

<?php $this->menu=array(
	Array('label'=>'Изменить границы моего участка', 'url'=>array('/profile/myarea'), 'linkOptions'=>array('class'=>'profileBtn')),
); ?>

<div class="lCol">

<?php $this->widget('application.widgets.userAreaMap.userAreaMapWidget',Array('data'=>Array('area'=>$area, 'user'=>$user->userModel),'model'=>$model)); ?>

<?php if ($user->userModel->areaNeighbors) : ?>
<div id="area_neighbors">
<h3>Соседи:</h3>
<ul>
<?php foreach ($user->userModel->areaNeighbors as $neighbor) : ?>
	<li><?php echo CHtml::link(CHtml::encode($neighbor->getParam('showFullname') ? $neighbor->Fullname : $neighbor->username), array('/profile/view', 'id'=>$neighbor->id),array('class'=>""));?></li>
<?php endforeach; ?>
</ul>	
</div>
<?php endif; ?>
<br/>
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
			<?php echo $form->dropDownList($model, 'TYPE_ID', CHtml::listData( HoleTypes::model()->findAll(Array('condition'=>'published=1', 'order'=>'ordering')), 'id','name'), array('prompt'=>'Тип дефекта')); ?>
			<?php echo $form->dropDownList($model, 'STATE', $model->Allstates, array('prompt'=>'Статус дефекта')); ?>
			<?php echo $form->dropDownList($model, 'showUserHoles', Array(1=>'Мои ямы', 2=>'Чужие, на которые я отправил заявление'),Array('prompt'=>'Все ямы')); ?>
			<?php echo CHtml::submitButton('Найти'); ?><br/>
			<div style="text-align:right;">
			<?php echo CHtml::checkBox('selectAll', false, Array('id'=>'selectAll','class'=>'state_check')); ?><?php echo CHtml::label('Выбрать все', 'selectAll'); ?>
			</div>
			<?php if ($model->keys) echo $form->dropDownList($model, 'gibdd_id', CHtml::listData(GibddHeads::model()->with(Array('holes'=>Array('select'=>'ID, gibdd_id')))->findAll(Array('condition'=>'holes.ID IN ('.implode(', ',$model->keys).')','order'=>'t.name')), 'id', 'gibdd_name' ), array('prompt'=>'Все ГИБДД')); ?>
			
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
		}',
	
)); ?>
</div>


