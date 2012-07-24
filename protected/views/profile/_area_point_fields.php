<?php echo $form->hiddenField($shape,"[$i]ordering", Array('class'=>'shape_ordering shape_'.$i, 'value'=>$i)); ?>
<?php echo $form->hiddenField($shape,"[$i]id", Array('class'=>'shape_id shape_'.$i)); ?>
		<?php for ($ii=0;$ii<$shape->countPoints;$ii++) : 
		if (isset($shape->points[$ii])) $areamodel=$shape->points[$ii];
		else $areamodel=new UserAreaShapePoints; ?>
		<div class="shape_point">
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]id", Array('class'=>'shape_'.$i)); ?>
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]lat", Array('class'=>'point_lat shape_'.$i)); ?>
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]lng", Array('class'=>'point_lng shape_'.$i)); ?>
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]point_num",Array('value'=>$ii, 'class'=>'shape_'.$i)); ?>
		</div>	
<?php endfor; ?>
