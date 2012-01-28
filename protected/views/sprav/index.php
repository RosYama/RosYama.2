<?
$this->pageTitle=Yii::app()->name . ' - Справочник ГИБДД';
$this->title='Справочник ГИБДД';
?>
<div class="news-list">
<?php if (!Yii::app()->user->isGuest) : ?>
<?php echo CHtml::link('Добавить территориальный отдел ГИБДД', array('add'), array('class'=>'')); ?>
<br/><br/><br/>
<?php endif; ?>
<?php foreach ($model as $subj) : ?>
			<p class="news-item">
				<?php if (Yii::app()->user->isModer && $subj->gibdd_local_not_moderated) : ?>
				<?php echo CHtml::link('('.($subj->id < 10 ? '0'.$subj->id : $subj->id).') '.CHtml::encode($subj->name_full."($subj->gibdd_local_not_moderated)"),Array('view','id'=>$subj->id), Array('style'=>'color:red;')); ?><br />
				<?php else : ?>
				<?php echo CHtml::link('('.($subj->id < 10 ? '0'.$subj->id : $subj->id).') '.CHtml::encode($subj->name_full),Array('view','id'=>$subj->id)); ?><br />
				<?php endif; ?>
			</p>
<?php endforeach; ?>				
</div>