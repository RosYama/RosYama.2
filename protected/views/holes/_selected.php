<div class="holes_select_list_box">
<?php if ($gibdds) : ?>
<h2>Выбраны:</h2>
<?php foreach ($gibdds as $gibdd) : ?>
<?php echo $gibdd->gibdd_name; ?><br/>
<?php //echo CHtml::link('Напечатать заявление на '.Y::declOfNum(count($gibdd->holes),Array('яму','ямы','ям')), Array('requestForm','id'=>$gibdd->id,'type'=>'gibdd','holes'=>implode(',',CHtml::listData($gibdd->holes,'ID','ID'))), Array('class'=>'show_form')); ?>
<?php echo CHtml::link('Сохранить список '.Y::declOfNum(count($gibdd->holes),Array('яму','ямы','ям')), Array('/profile/saveHoles2Selected','id'=>$gibdd->id,'holes'=>implode(',',CHtml::listData($gibdd->holes,'ID','ID'))), Array('class'=>'save_selected')); ?><br/><br/>
<?php endforeach; ?>
<br/><?php echo CHtml::link('Очистить список','#',Array('class'=>'clear_selected')); ?>
<br/><br/>
<?php endif; ?>
<?php 
if ($user->selected_holes_lists) : ?>
<h2>Сохраненные:</h2>
<?php foreach ($user->selected_holes_lists as $list) : ?>
	<?php echo Y::dateFromTimeShort($list->date_created); ?> <?php echo CHtml::link('удалить', Array('/profile/delHolesSelectList','id'=>$list->id), Array('class'=>'save_selected')); ?><br/>
	<?php echo CHtml::link('Заявление', Array('requestForm','id'=>$list->gibdd_id,'type'=>'gibdd','holes'=>implode(',',CHtml::listData($list->holes,'ID','ID'))), Array('class'=>'show_form')); ?><br/>
	<?php if ($list->notSentHoles) : ?>
	<?php echo CHtml::link('Пометить как отправленное '.Y::declOfNum(count($list->notSentHoles),Array('яму','ямы','ям')), Array('sentMany','holes'=>implode(',',CHtml::listData($list->notSentHoles,'ID','ID'))), Array('class'=>'')); ?><br/>
	<?php endif; ?>
	<?php if ($list->sentedHoles) : ?>
	<?php echo CHtml::link('Загрузить ответ на '.Y::declOfNum(count($list->sentedHoles),Array('яму','ямы','ям')), Array('gibddreply','holes'=>implode(',',CHtml::listData($list->sentedHoles,'ID','ID'))), Array('class'=>'')); ?><br/>
	<?php endif; ?>
	<br/>
<?php endforeach; ?>  
<?php endif; ?>
</div>