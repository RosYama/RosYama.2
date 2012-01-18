<?
$this->pageTitle=Yii::app()->name . ' - '.$model->name_full.' - Справочник ГИБДД ';
$this->title=CHtml::link('Справочник ГИБДД', Array('index')).' > '.$model->name_full;
?>
<div class="mainCols">
<?php if ($model->gibdd) : ?>
<div class="news-detail">
				<h2><?php echo $model->gibdd->gibdd_name; ?></h2>
				
						<div style="clear:both"></div>
		 				ФИО:&nbsp;<?php echo $model->gibdd->fio; ?><br />
	  		
	 				Должность:&nbsp;<?php echo $model->gibdd->post; ?><br />
	  		
	 				Адрес:&nbsp;<?php echo $model->gibdd->address; ?><br />
	  		
	 				Телефон дежурной части:&nbsp;<?php echo $model->gibdd->tel_degurn; ?><br />
	  		
	 				Телефон доверия:&nbsp;<?php echo $model->gibdd->tel_dover; ?><br />
	  		
	 				Сайт:&nbsp;<?php echo $model->gibdd->link; ?><br />
	  		
		</div>
<br/><br/>
<?php endif; ?>			
<?php if ($model->prosecutor) : ?>
<div class="news-detail">
				<h2><?php echo $model->prosecutor->gibdd_name; ?></h2>
				<?php echo $model->prosecutor->preview_text; ?><div style="clear:both"></div>
		 				
		</div>
<?php endif; ?>			
</div>