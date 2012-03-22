<div class="holes_select_list_box">
<?php foreach ($gibdds as $gibdd) : ?>
<?php echo $gibdd->gibdd_name; ?><br/>
<?php echo CHtml::link('Напечатать заявление на '.Y::declOfNum(count($gibdd->holes),Array('яму','ямы','ям')), Array('requestForm','id'=>$gibdd->id,'type'=>'gibdd','holes'=>implode(',',CHtml::listData($gibdd->holes,'ID','ID'))), Array('class'=>'show_form')); ?><br/>
<?php endforeach; ?>
<br/><br/><?php echo CHtml::link('Очистить список','#',Array('class'=>'clear_selected')); ?>
</div>