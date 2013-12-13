<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
				'id'=>'dorogimosDialog',
				// additional javascript options for the dialog plugin
				'options'=>array(
					'title'=>'Сообщить через gorod.mos.ru',
					'autoOpen'=>$model->errors || (isset($_GET['fromadd']) && $_GET['fromadd']) ? true : false,
					'width'=>'auto',
					'height'=>'auto',
					'resizable'=>false,
					'modal'=>true,
					'buttons'=>'js:[
						'.($model->todaySended <= $model->maxTodayCount ? '
						{
							text: "Отправить",
							click: function(){
								$("#dorogimos-request-form").submit();
							}
						},' : '').'
						{
							text: "Закрыть",
							click: function(){
								$(this).dialog("close");
								return false;
							}
						}
					]'
				),
			)); ?>
		<?php if ($model->todaySended <= $model->maxTodayCount) : ?>
			<div class="wide form">
			<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'dorogimos-request-form',
				'enableAjaxValidation'=>false,
			)); 
			if (!$model->surname) $model->surname=$user->last_name;
			if (!$model->name) $model->name=$user->name;
			if (!$model->fatherName) $model->fatherName=$user->second_name;
			if (!$model->email) $model->email=$user->email;
			if (!$model->address) $model->address=$user->relProfile->request_address ? $user->relProfile->request_address : '';
			if (!$model->phoneNumber) $model->phoneNumber=$user->relProfile->request_phone ? $user->relProfile->request_phone : '';
			if (!$model->phoneNumber) $model->notifyViaSms=0;
			if (!$model->holeAddress) $model->holeAddress=$hole->ADDRESS;
			if (!$model->details) $model->details=$hole->description_size.' '.$hole->description_locality.'('.$hole->COMMENT1.')';
			?>
			<p class="note"><strong>за сегодня отправлено <?php echo $model->todaySended; ?> из возможных <?php echo $model->maxTodayCount; ?>-ти заявлений</strong></p>
			<p class="note">Поля отмеченные <span class="required">*</span> являются обязательными.</p>
			<?php echo $form->errorSummary($model); ?>
			<div class="row">
				<?php echo $form->labelEx($model,'surname'); ?>
				<?php echo $form->textField($model,'surname',array('size'=>60)); ?>
				<?php echo $form->error($model,'surname'); ?>
			</div>				
			<div class="row">
				<?php echo $form->labelEx($model,'name'); ?>
				<?php echo $form->textField($model,'name',array('size'=>60)); ?>
				<?php echo $form->error($model,'name'); ?>
			</div>	
			<div class="row">
				<?php echo $form->labelEx($model,'fatherName'); ?>
				<?php echo $form->textField($model,'fatherName',array('size'=>60)); ?>
				<?php echo $form->error($model,'fatherName'); ?>
			</div>	
			<div class="row">
				<?php echo $form->labelEx($model,'email'); ?>
				<?php echo $form->textField($model,'email',array('size'=>60)); ?>
				<?php echo $form->error($model,'email'); ?>
			</div>
			<div class="row">
				<?php echo $form->labelEx($model,'phoneNumber'); ?>
				<?php echo $form->textField($model,'phoneNumber',array('size'=>60)); ?>
				<?php echo $form->error($model,'phoneNumber'); ?>
			</div>
			<div class="row">
				<?php echo $form->labelEx($model,'address'); ?>
				<?php echo $form->textField($model,'address',array('size'=>60)); ?>
				<?php echo $form->error($model,'address'); ?>
			</div>
			<div class="row">
				<?php echo $form->labelEx($model,'notifyViaEmail'); ?>
				<div style="text-align:left;">
				<?php echo $form->checkBox($model,'notifyViaEmail'); ?>
				<?php echo $form->error($model,'notifyViaEmail'); ?>
				</div>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'notifyViaSms'); ?>
				<div style="text-align:left;">
				<?php echo $form->checkBox($model,'notifyViaSms'); ?>
				<?php echo $form->error($model,'notifyViaSms'); ?>
				</div>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'holeAddress'); ?>
				<?php echo $form->textField($model,'holeAddress',array('size'=>60)); ?>
				<?php echo $form->error($model,'holeAddress'); ?>
			</div>
			
			<div class="row">
				<?php echo $form->labelEx($model,'details'); ?>
				<?php echo $form->textArea($model,'details',array('cols'=>60,'rows'=>5)); ?>
				<?php echo $form->error($model,'details'); ?>
			</div>
			<?php $this->endWidget(); ?>	
			</div>
		<?php else : ?>		
		<p>Порталы Мэрии Москвы принимают максимум по <?php echo $model->maxTodayCount; ?> заявлений в день от человека. <br />Приходите завтра. <br />Спасибо за понимание.</p>
		<?php endif; ?>	
			<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>