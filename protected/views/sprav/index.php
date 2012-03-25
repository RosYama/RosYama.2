<?
$this->pageTitle=Yii::app()->name . ' - Справочник ГИБДД';
$this->title='Справочник ГИБДД';
?>
<div class="news-list">
<?php foreach ($model as $subj) : ?>
			<p class="news-item">
				<?php if (Yii::app()->user->isModer && $subj->gibdd_local_not_moderated) : ?>
				<?php echo CHtml::link('('.($subj->region_num < 10 ? '0'.$subj->region_num : $subj->region_num).') '.CHtml::encode($subj->name_full."($subj->gibdd_local_not_moderated)"),Array('view','id'=>$subj->id), Array('style'=>'color:red;')); ?><br />
				<?php else : ?>
				<?php echo CHtml::link('('.($subj->region_num < 10 ? '0'.$subj->region_num : $subj->region_num).') '.CHtml::encode($subj->name_full),Array('view','id'=>$subj->id)); ?><br />
				<?php endif; ?>
			</p>
<?php endforeach; ?>				
</div>