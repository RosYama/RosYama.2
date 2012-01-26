<?php echo $form->hiddenField($shape,"[$i]id", Array('class'=>'shape_id')); ?>
		<?php for ($ii=0;$ii<$shape->countPoints;$ii++) : 
		if (isset($shape->points[$ii])) $areamodel=$shape->points[$ii];
		else $areamodel=new UserAreaShapePoints; ?>
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]id"); ?>
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]lat"); ?>
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]lng"); ?>
			<?php echo $form->hiddenField($areamodel,"[$i][$ii]point_num",Array('value'=>$ii)); ?>
<?php endfor; ?>