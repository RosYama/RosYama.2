<? 
$this->pageTitle=Yii::app()->name . ' - '.$model->name_full.' - Справочник ГИБДД ';
$this->title=CHtml::link('Справочник ГИБДД', Array('index')).' > '.$model->name_full;
?>
<?php if ($model->gibdd) : ?>
<div class="news-detail">
<?php $this->renderPartial('_view_gibdd', array('data'=>$model->gibdd)); ?>	  		
</div>
<br/><br/>
<?php endif; ?>			
<?php if ($model->prosecutor) : ?>
<div class="news-detail">
				<h2><?php echo $model->prosecutor->gibdd_name; ?></h2>
				<?php echo $model->prosecutor->preview_text; ?><div style="clear:both"></div>
				<?php if ($model->prosecutor->url_priemnaya): ?>
	 				Интернет-приемная:&nbsp;<?php echo CHtml::link($model->prosecutor->url_priemnaya, $model->prosecutor->url_priemnaya); ?><br />
 				<?php endif; ?>
		 		<?php if (!Yii::app()->user->isGuest && Yii::app()->user->isAdmin) : ?>
				<?php echo CHtml::link('редактировать', array('updateprosecutor','id'=>$model->prosecutor->id)); ?>
				<?php endif; ?>			
		</div>
<?php endif; ?>		

<?php if (!Yii::app()->user->isGuest) : ?>
<br/><br/><br/>
<?php echo CHtml::link('Добавить территориальный отдел ГИБДД', array('add'), array('class'=>'')); ?>
<?php endif; ?>
<?php if ($model->gibdd_local) : ?>
<br/><br/>
<h2>Территориальные отделы ГИБДД :</h2>
<?php foreach ($model->gibdd_local as $data) : ?>
<div class="news-detail">
				<?php $this->renderPartial('_view_gibdd', array('data'=>$data)); ?>		 				
		</div>
<br/><br/>		
<?php endforeach; ?>				
<?php endif; ?>		
