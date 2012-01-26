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
				<?php echo CHtml::link('('.($subj->id < 10 ? '0'.$subj->id : $subj->id).') '.CHtml::encode($subj->name_full),Array('view','id'=>$subj->id)); ?><br />	
			</p>
<?php endforeach; ?>				
</div>