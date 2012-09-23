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
<div class="head">
		<div class="container">
		<div class="lCol">
					<a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="Логотип" /></a>
			</div>
			<div class="rCol">
	<div class="h">
		<div class="info">
			<h1><span class="date">Сохраненный список ям от <?php echo CHtml::encode(Y::dateFromTimeShort($list->date_created)); ?></span></h1>
			<p>Отдел ГИБДД: "<?php echo $list->gibdd->gibdd_name; ?>"</p>
			<p><?php echo CHtml::link('изменить', '#', Array('onclick'=>'$("#change_gibdd").show("slow"); $(this).hide("slow"); return false;')); ?></p>
			<div id="change_gibdd" style="display:none;">
			<form method="post">
			<?php echo CHtml::DropDownList('gibdd_change_id', '', CHtml::listData(GibddHeads::model()->findAll(Array('condition'=>'t.subject_id='.$list->gibdd->subject_id.' AND t.id !='.$list->gibdd->id,'order'=>'t.name')), 'id', 'gibdd_name' ),
			array ());
			?>
			<?php echo CHtml::submitButton('Изменить'); ?>
			</form>

			
			</div>
			
			<div class="control">			
				<div class="progress">
							<div class="lc">
							<?php echo CHtml::link('Распечатать заявление', Array('requestForm','id'=>$list->gibdd_id,'type'=>'gibdd','holes'=>implode(',',CHtml::listData($list->holes,'ID','ID'))), Array('class'=>'show_form')); ?>
							</div>
							<?php if ($list->notSentHoles) : ?>
							<div class="cc">
							<?php echo CHtml::link('Пометить как отправленное '.Y::declOfNum(count($list->notSentHoles),Array('яму','ямы','ям')), Array('sentMany','holes'=>implode(',',CHtml::listData($list->notSentHoles,'ID','ID'))), Array('class'=>'')); ?><br/>
							</div>
							<?php endif; ?>
							<?php if ($list->sentedHoles) : ?>
							<div class="rc" style="padding: 24px 15px;">
							<?php echo CHtml::link('Загрузить ответ на '.Y::declOfNum(count($list->sentedHoles),Array('яму','ямы','ям')), Array('gibddreply','holes'=>implode(',',CHtml::listData($list->sentedHoles,'ID','ID'))), Array('class'=>'')); ?><br/>
							</div>
							<?php endif; ?>
							
				</div>
			</div>	
		</div>
	</div>
</div>		
<!-- CLOSE HEAD CONTAINER -->
</div>
<!-- CLOSE HEAD -->
</div>
<div class="mainCols" id="col">		
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
	//'action'=>Yii::app()->createUrl($this->route),
	//'method'=>'get',
	'id'=>'holes_selectors',
)); ?>			
			<?php echo $form->dropDownList($model, 'TYPE_ID', CHtml::listData( HoleTypes::model()->findAll(Array('condition'=>'published=1', 'order'=>'ordering')), 'id','name'), array('prompt'=>'Тип дефекта')); ?>
			<?php echo $form->dropDownList($model, 'STATE', $model->Allstates, array('prompt'=>'Статус дефекта')); ?>
			<?php echo $form->dropDownList($model, 'showUserHoles', Array('3'=>'Все ямы', 1=>'Мои ямы', 2=>'Чужие, на которые я отправил заявление')); ?>
			<?php echo CHtml::submitButton('Найти'); ?><br/>
			
	<?php $this->endWidget(); ?>		
			</p>
				
<?php $this->widget('zii.widgets.CListView', array(
	'id'=>'holes_list',
	'ajaxUpdate'=>true,
	'dataProvider'=>$model->userSearch(),
	'itemView'=>'_view',
	'itemsTagName'=>'ul',
	'cssFile'=>'/css/holes_list.css',
	'itemsCssClass'=>'holes_list',
	'summaryText'=>false,
	'viewData'=>Array('showcheckbox'=>false, 'user'=>$user),
	'afterAjaxUpdate'=> 'function(id){
		checkInList();
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
</div>

