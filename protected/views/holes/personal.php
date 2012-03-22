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

			$(".holes_list").each(function() {  checkInState($(this));	});	
			
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
	<?php if ($selected=Yii::app()->user->getState('selectedHoles', Array())) : ?>
		<?php 
		$this->renderPartial('_selected', Array('gibdds'=>GibddHeads::model()->with('holes')->findAll('holes.id IN ('.implode(',',$selected).')')));
		?>
	<?php endif; ?>
	</div>

</div>

<div class="rCol">
<div class="pdf_form" id="pdf_form" style="display: none; left:auto;">
				<a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
				<div id="gibdd_form"></div>
				</div>
<?php foreach ($model->AllstatesMany as $state_alias=>$state_name) : ?>
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
<?php endforeach; ?>
</div>


