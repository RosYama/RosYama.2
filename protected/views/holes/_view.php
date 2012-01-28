<li<?php if(($index+1)%3==0):?> class="noMargin"<?php endif; ?>>
			<?php echo CHtml::link(CHtml::image($data->STATE == 'fixed' && $data->pictures_fixed ? $data->pictures_fixed[0]->small : $data->pictures_fresh[0]->small), array('view', 'id'=>$data->ID), array('class'=>'photo')); ?>			
			<?php if(Yii::app()->user->isModer): ?>
				<?php if(!$data->PREMODERATED): ?>
					<div class="premoderate" id="premoderate_<?php echo $data->ID ?>"><img src="/images/st1234/iconpm.gif" onclick="setPM_OK('<?php echo $data->ID ?>');" title="Показывать этот дефект всем"></div>
				<?php endif; ?>
				<div class="del"><a title="Удалить дефект" href="#" onclick="ShowDelForm(this, '<?php echo $data->ID ?>'); return false;"><img src="/images/st1234/icondel.gif"></a></div>
			<?php endif; ?>
			<div class="properties">
				<p class="date"><?php echo CHtml::encode(Y::dateFromTime($data->DATE_CREATED)); ?></p>
				<div class="service"><?php echo CHtml::encode($data->ADDRESS); ?><i></i></div>
				<div class="social">
					<img src="/images/st1234/<?php echo CHtml::encode($data->type->alias); ?>.png" title="<?php echo CHtml::encode($data->type->name); ?>">
					<span class="status_span state_<?= $data->STATE ?>">&bull;</span>
					<span class="status_text"><?php echo CHtml::encode($data->StateName); ?></span>
					<?php  if($data->WAIT_DAYS): ?>
						<span class="status_days"><i>ждать <?php echo Y::declOfNum($data->WAIT_DAYS, array('день', 'дня', 'дней')); ?></i></span>
					<?php endif; ?>
					<?php  if($data->PAST_DAYS): ?>
						<span class="status_days"><i>просрочено <?php //echo Y::declOfNum($data->PAST_DAYS, array('день', 'дня', 'дней')); ?></i></span>
					<?php endif; ?>
					<?php /* if($elem['PAST_DAYS']): ?>
						<span class="status_days"><i><?php echo $elem['PAST_DAYS'] ?></i></span>
					<? endif; */ ?>
				</div>
			</div>
</li>
	<?php /*		
<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('ID')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->ID), array('view', 'id'=>$data->ID)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('USER_ID')); ?>:</b>
	<?php echo CHtml::encode($data->USER_ID); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('LATITUDE')); ?>:</b>
	<?php echo CHtml::encode($data->LATITUDE); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('LONGITUDE')); ?>:</b>
	<?php echo CHtml::encode($data->LONGITUDE); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ADDRESS')); ?>:</b>
	<?php echo CHtml::encode($data->ADDRESS); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('STATE')); ?>:</b>
	<?php echo CHtml::encode($data->STATE); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DATE_CREATED')); ?>:</b>
	<?php echo CHtml::encode($data->DATE_CREATED); ?>
	<br />


	<b><?php echo CHtml::encode($data->getAttributeLabel('DATE_SENT')); ?>:</b>
	<?php echo CHtml::encode($data->DATE_SENT); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DATE_STATUS')); ?>:</b>
	<?php echo CHtml::encode($data->DATE_STATUS); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('COMMENT1')); ?>:</b>
	<?php echo CHtml::encode($data->COMMENT1); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('COMMENT2')); ?>:</b>
	<?php echo CHtml::encode($data->COMMENT2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('TYPE')); ?>:</b>
	<?php echo CHtml::encode($data->TYPE); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ADR_SUBJECTRF')); ?>:</b>
	<?php echo CHtml::encode($data->ADR_SUBJECTRF); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ADR_CITY')); ?>:</b>
	<?php echo CHtml::encode($data->ADR_CITY); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('COMMENT_GIBDD_REPLY')); ?>:</b>
	<?php echo CHtml::encode($data->COMMENT_GIBDD_REPLY); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('GIBDD_REPLY_RECEIVED')); ?>:</b>
	<?php echo CHtml::encode($data->GIBDD_REPLY_RECEIVED); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PREMODERATED')); ?>:</b>
	<?php echo CHtml::encode($data->PREMODERATED); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('DATE_SENT_PROSECUTOR')); ?>:</b>
	<?php echo CHtml::encode($data->DATE_SENT_PROSECUTOR); ?>
	<br />

	

</div> */ ?>