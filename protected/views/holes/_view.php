<li<?php if(($index+1)%3==0):?> class="noMargin"<?php endif; ?>>
			<?php echo CHtml::link(CHtml::image($data->STATE == 'fixed' && $data->pictures_fixed ? $data->pictures_fixed[0]->small : ($data->pictures_fresh ? $data->pictures_fresh[0]->small:'')), array('view', 'id'=>$data->ID), array('class'=>'photo')); ?>
			<?php if (isset($showcheckbox) && $showcheckbox) : ?>
				<?php echo CHtml::checkBox('hole_id[]', $data->isSelected ? true : false, Array('value'=>$data->ID, 'class'=>'hole_check')); ?>
			<?php endif; ?>
			<?php if($user->isModer): ?>
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