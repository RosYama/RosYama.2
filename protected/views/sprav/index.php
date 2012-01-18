<?
$this->pageTitle=Yii::app()->name . ' - Справочник ГИБДД';
$this->title='Справочник ГИБДД';
?>
<div class="mainCols"><div class="news-list">
<?php foreach ($model as $subj) : ?>
			<p class="news-item">
				<?php echo CHtml::link('('.($subj->id < 10 ? '0'.$subj->id : $subj->id).') '.CHtml::encode($subj->name_full),Array('view','id'=>$subj->id)); ?><br />	
			</p>
<?php endforeach; ?>			
</div>			
</div>