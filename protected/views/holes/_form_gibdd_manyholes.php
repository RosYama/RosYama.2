			
					<!--Не исключена вероятность того, что на <a href="http://www.gosuslugi.ru/ru/chorg/index.php?ssid_4=4120&stab_4=4&rid=228&tid=2" target="_blank">сайте госуслуг</a> окажется немного полезной информации.-->
					<div class="wide form gibdd_form">
					<?php $form=$this->beginWidget('CActiveForm', array(
						'id'=>'request-form',
						'enableAjaxValidation'=>false,
						'action'=>Yii::app()->createUrl("holes/sendToGibddru"),
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
			
			<?php echo $form->hiddenField($gibddModel,'form_text_31'); ?>
			<?php echo $form->hiddenField($gibddModel,'sessid'); ?>
			<?php echo $form->hiddenField($gibddModel,'reg'); ?>			
			<?php echo $form->hiddenField($gibddModel,'tmp'); ?>
			<?php echo $form->hiddenField($gibddModel,'sbj'); ?>			
			<?php echo $form->hiddenField($gibddModel,'pst'); ?>		
			<?php echo $form->hiddenField($gibddModel,'form_hidden_40'); ?>		
			<?php echo $form->hiddenField($gibddModel,'holes'); ?>	
			<?php foreach ($holes as $hole) {
				echo $form->hiddenField($model,'holes[]',Array('value'=>$hole->ID));
				echo $form->hiddenField($gibddModel,'holes[]',Array('value'=>$hole->ID));
				} ?>
				<div class="row">
					<?php echo $form->labelEx($model,'to'); ?>
					<?php echo $form->textArea($model,'to',array('rows'=>3, 'cols'=>40,'class'=>"textInput")); ?>
					<p class="hint"><?php echo Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_COMMENT'); ?></p>
					<?php echo $form->error($gibddModel,'form_text_11'); ?>
				</div>
				<h2 style="text-align:center;">От</h2>		
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_11'); ?>
					<?php echo $form->textField($gibddModel,'form_text_11',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_text_11'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_12'); ?>
					<?php echo $form->textField($gibddModel,'form_text_12',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_text_12'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_13'); ?>
					<?php echo $form->textField($gibddModel,'form_text_13',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_text_13'); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_14'); ?>
					<?php echo $form->textField($gibddModel,'form_text_14',array('maxlength'=>10,'class'=>"textInput", 'style'=>'width:50px;')); ?>
					<?php echo $form->error($gibddModel,'form_text_14'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_15'); ?>
					<?php echo $form->textField($gibddModel,'form_text_15',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_text_15'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_16'); ?>
					<?php echo $form->textField($gibddModel,'form_text_16',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_text_16'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_17'); ?>
					<?php echo $form->textField($gibddModel,'form_text_17',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_text_17'); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_email_18'); ?>
					<?php echo $form->textField($gibddModel,'form_email_18',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_email_18'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_text_19'); ?>
					<?php echo $form->textField($gibddModel,'form_text_19',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_text_19'); ?>
				</div>
					
					<?php echo $form->hiddenField($gibddModel,'form_dropdown_SUBJECT'); ?>				
					<?php //echo $form->hiddenField($model,'form_type', Array('value'=>'gibdd')); ?>	

				
				<h2 style="text-align:center;"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM') ?></h2>		
			
				<div class="row">
					<?php echo $form->labelEx($gibddModel,'form_textarea_26'); ?>
					<?php echo $form->textArea($gibddModel,'form_textarea_26',array('rows'=>10,'class'=>"textInput")); ?>
					<?php echo $form->error($gibddModel,'form_textarea_26'); ?>
					<div class="fileButtons">							
							<p><?php echo CHtml::link('Скачать заявление в виде ПДФ', '#', Array('class'=>'printDeclaration downloadPdf', 'hole_id'=>implode(',', CHtml::listData($holes, 'ID', 'ID')))); ?></p>
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
					<?php echo CHtml::image($this->createUrl('getGibddCaptcha', Array('sid'=>$gibddModel->captcha_sid))); ?>
					<?php echo $form->hiddenField($gibddModel,'captcha_sid'); ?>
					<?php echo $form->labelEx($gibddModel,'captcha_word'); ?>
					<?php echo $form->textField($gibddModel,'captcha_word', Array('style'=>'width: 200px;')); ?>
					<?php echo $form->error($gibddModel,'captcha_word'); ?>
				</div>
			
			
				<div class="row buttons">
					<?php echo CHtml::submitButton($gibddModel->web_form_submit, Array('name'=>'web_form_submit')); ?>
				</div>	
					<?php $this->endWidget(); ?>
				</div>