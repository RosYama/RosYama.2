<h2><?php echo $data->gibdd_name; ?></h2>
				
						<div style="clear:both"></div>
		 				ФИО:&nbsp;<?php echo $data->fio; ?><br />
	  		
	 				Должность:&nbsp;<?php echo $data->post; ?><br />
	  		
	 				Адрес:&nbsp;<?php echo $data->address; ?><br />
	  		
	 				Телефон дежурной части:&nbsp;<?php echo $data->tel_degurn; ?><br />
	  		
	 				Телефон доверия:&nbsp;<?php echo $data->tel_dover; ?><br />
	  		
	 				Сайт:&nbsp;<?php echo $data->link; ?><br />

<?php if (!Yii::app()->user->isGuest && (Yii::app()->user->id==$data->author_id || Yii::app()->user->isModer) && $data->is_regional==0) : ?>
<br/>
<?php echo CHtml::link('редактировать', array('update','id'=>$data->id)); ?>
	<?php echo CHtml::link('удалить', array('delete','id'=>$data->id)); ?>
	<?php if (!$data->moderated && Yii::app()->user->isModer) : ?>
		<?php echo CHtml::link('модерировать', array('moderate','id'=>$data->id)); ?>
	<?php endif; ?>	  
<?php endif; ?>	  