<hr />
					<?php  if($data->WAIT_DAYS): ?>
						<span class="status_days"><i>ждать <?php echo Y::declOfNum($data->WAIT_DAYS, array('день', 'дня', 'дней')); ?></i></span><br />
					<?php endif; ?>
					<?php  if($data->PAST_DAYS): ?>
						<span class="status_days"><i>просрочено <?php echo Y::declOfNum($data->PAST_DAYS, array('день', 'дня', 'дней')); ?></i></span><br />
					<?php endif; ?>
			<?php echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl.($data->STATE == 'fixed' && $data->pictures_fixed ? $data->pictures_fixed[0]->small : ($data->pictures_fresh ? $data->pictures_fresh[0]->small:''))), array('view', 'id'=>$data->ID), array('class'=>'photo')); ?>
			<div class="properties">
				<p class="date">Дата добавления ямы: <?php echo CHtml::encode(Y::dateFromTime($data->DATE_CREATED)); ?></p>
				<div class="service"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/st1234/<?php echo CHtml::encode($data->type->alias); ?>.png" title="<?php echo CHtml::encode($data->type->name); ?>"><?php echo CHtml::encode($data->ADDRESS); ?><i></i></div>
				<p>Ссылка: <?php echo CHtml::link(CController::createUrl('/holes/view', array('id'=>$data->ID)), Array('/holes/view','id'=>$data->ID),array('class'=>'photo')); ?></p>
					<?php /*<span class="status_text"><?php echo CHtml::encode($data->StateName); ?></span>
					 if($elem['PAST_DAYS']): ?>
						<span class="status_days"><i><?php echo $elem['PAST_DAYS'] ?></i></span>
					<? endif; */ ?>
				</div>
			</div>
