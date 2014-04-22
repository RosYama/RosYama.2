					<!--Не исключена вероятность того, что на <a href="http://www.gosuslugi.ru/ru/chorg/index.php?ssid_4=4120&stab_4=4&rid=228&tid=2" target="_blank">сайте госуслуг</a> окажется немного полезной информации.-->
					<div class="wide form gibdd_form">
					<?php $form=$this->beginWidget('CActiveForm', array(
						'id'=>'request-form',
						'enableAjaxValidation'=>false,
						'action'=>Yii::app()->createUrl("holes/sendToGibddru", Array('many'=>false)),
						'htmlOptions'=>Array ('onsubmit'=>"document.getElementById('pdf_form').style.display='none';"),
					)); 
					$usermodel=Yii::app()->user->userModel;
					?>				
			<?php echo $form->errorSummary($gibddModel); ?> 
			<?php if ($error) : ?>
			<div class="errorSummary">​
				<p>​Необходимо исправить следующие ошибки:​</p>​
				<?php echo $error; ?>
			</div>
			
			<?php endif; ?>
			
			<?php echo $form->hiddenField($gibddModel,'sessid'); ?>
			<?php echo $form->hiddenField($gibddModel,'f_gai_regkod'); ?>
			<?php echo $form->hiddenField($gibddModel,'f_token'); ?>


			<?php echo $form->hiddenField($gibddModel,'holes'); ?>	
			
				<div class="row">
					<?php echo $form->labelEx($model,'to'); ?>
					<?php echo $form->textArea($model,'to',array('rows'=>3, 'cols'=>40,'class'=>"textInput")); ?>
					<p class="hint"><?php echo Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_COMMENT'); ?></p>
					<?php echo $form->error($model,'to'); ?>
				</div>
				<h2 style="text-align:center;">От</h2>		
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_fam'); ?>
					<?php echo $form->textField($gibddModel,'f_fam',array('maxlength'=>40,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_fam'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_name'); ?>
					<?php echo $form->textField($gibddModel,'f_name',array('maxlength'=>40,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_name'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_coname'); ?>
					<?php echo $form->textField($gibddModel,'f_coname',array('maxlength'=>40,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_coname'); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_ind'); ?>
					<?php echo $form->textField($gibddModel,'f_ind',array('maxlength'=>10,'class'=>"textInput", 'style'=>'width:50px;')); ?>
					<?php echo $form->error($gibddModel,'f_ind'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_reg'); ?>
					<?php echo $form->textField($gibddModel,'f_reg',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_reg'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_npunkt'); ?>
					<?php echo $form->textField($gibddModel,'f_npunkt',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_npunkt'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_addr'); ?>
					<?php echo $form->textField($gibddModel,'f_addr',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_addr'); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_email'); ?>
					<?php echo $form->textField($gibddModel,'f_email',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_email'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_phone'); ?>
					<?php echo $form->textField($gibddModel,'f_phone',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_phone'); ?>
				</div>					
	
				<h2 style="text-align:center;"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM') ?></h2>		
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'f_msg'); ?>
					<?php echo $form->textArea($gibddModel,'f_msg',array('rows'=>10,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'f_msg'); ?>
					<div class="fileButtons">							
							<p><?php echo CHtml::link('Скачать заявление в виде ПДФ', '#', Array('class'=>'printDeclaration downloadPdf', 'hole_id'=>$hole->ID)); ?></p>
							<!--
							<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT'), Array('class'=>'submit', 'name'=>'HoleRequestForm[pdf]')); ?>
							<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2'), Array('class'=>'submit', 'name'=>'HoleRequestForm[html]')); ?>
							-->
					</div>
				</div>
				<div class="row">
					<?php echo  $form->labelEx($model,'address'); ?>
					<?php echo $form->textArea($model,'address',array('rows'=>3, 'cols'=>40)); ?>
					<p class="hint"><?php echo Yii::t('holes_view', 'HOLE_REQUEST_FORM_ADDRESS_COMMENT'); ?></p>
					<?php echo $form->error($gibddModel,'address'); ?>
				</div>
				
				<div class="row">					  
					<?php echo CHtml::image($this->createUrl('getGibddCaptcha', Array('sid'=>$gibddModel->captcha_code))); ?>
					<?php //echo CHtml::image('http://www.gibdd.ru/bitrix/tools/captcha.php?captcha_sid='.$gibddModel->captcha_sid); ?>					
					<?php echo $form->hiddenField($gibddModel,'captcha_code'); ?>
					<?php echo $form->labelEx($gibddModel,'captcha_word'); ?>
					<?php echo $form->textField($gibddModel,'captcha_word', Array('style'=>'width: 200px;')); ?>
					<?php echo $form->error($gibddModel,'captcha_word'); ?>
				</div>
			
			
				<div class="row buttons">
					<?php echo CHtml::submitButton($gibddModel->web_form_submit, Array('name'=>'web_form_submit')); ?>
				</div>	
					<?php $this->endWidget(); ?>
				</div>