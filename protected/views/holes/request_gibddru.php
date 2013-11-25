<?php 
$this->pageTitle=Yii::app()->name . ':: Отправка запроса в приемную ГИБДД';
$this->title='Отправка запроса в приемную ГИБДД';
?>
<div class="mainCols">
	<div id="userGroups-container">
		<div class="form">
		
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'gibdd-ru-form-_form_gibddru-form',
			'enableAjaxValidation'=>false,
			'action'=>$this->createUrl('sendToGibddru'),
		)); ?>
		
			<p class="note">Поля отмеченные <span class="required">*</span> являются обязательными.</p>
		
			<?php echo $form->errorSummary($model); ?> 
			<?php if ($error) : ?>
			<div class="errorSummary">​
				<p>​Необходимо исправить следующие ошибки:​</p>​
				<?php echo $error; ?>
			</div>
			
			<?php endif; ?>
			
			<?php echo $form->hiddenField($model,'form_text_31'); ?>
			<?php echo $form->hiddenField($model,'sessid'); ?>
			<?php echo $form->hiddenField($model,'reg'); ?>			
			<?php echo $form->hiddenField($model,'tmp'); ?>
			<?php echo $form->hiddenField($model,'sbj'); ?>			
			<?php echo $form->hiddenField($model,'pst'); ?>		
			<?php echo $form->hiddenField($model,'form_hidden_40'); ?>		
			<?php echo $form->hiddenField($model,'holes'); ?>	
			
			<div class="profileTable">
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_11'); ?>
					<?php echo $form->textField($model,'form_text_11',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_text_11'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_12'); ?>
					<?php echo $form->textField($model,'form_text_12',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_text_12'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_13'); ?>
					<?php echo $form->textField($model,'form_text_13',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_text_13'); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_14'); ?>
					<?php echo $form->textField($model,'form_text_14',array('maxlength'=>10,'class'=>"textInput", 'style'=>'width:50px;')); ?>
					<?php echo $form->error($model,'form_text_14'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_15'); ?>
					<?php echo $form->textField($model,'form_text_15',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_text_15'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_16'); ?>
					<?php echo $form->textField($model,'form_text_16',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_text_16'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_17'); ?>
					<?php echo $form->textField($model,'form_text_17',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_text_17'); ?>
				</div>
				
				<div class="row">
					<?php echo $form->labelEx($model,'form_email_18'); ?>
					<?php echo $form->textField($model,'form_email_18',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_email_18'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_text_19'); ?>
					<?php echo $form->textField($model,'form_text_19',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_text_19'); ?>
				</div>
					
					<?php echo $form->hiddenField($model,'form_dropdown_SUBJECT'); ?>
				
				
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_textarea_26'); ?>
					<?php echo $form->textArea($model,'form_textarea_26',array('rows'=>10,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_textarea_26'); ?>
				</div>
			
				<div class="row">
					<?php echo $form->labelEx($model,'form_file_27'); ?>
					<?php echo CHtml::link(CHtml::image('/images/icon_application_pdf.png', 'Заявление', Array('title'=>'Заявление')), $model->form_file_27, Array('class'=>'declarationBtn')); ?>
					<?php echo $form->hiddenField($model,'form_file_27'); ?>
					<?php //echo $form->textField($model,'form_file_27',array('maxlength'=>50,'class'=>"textInput")); ?>
					<?php echo $form->error($model,'form_file_27'); ?>
				</div>			

				
				<div class="row">					
					<?php echo CHtml::image('http://www.gibdd.ru/bitrix/tools/captcha.php?captcha_sid='.$model->captcha_sid); ?>
					<?php echo $form->hiddenField($model,'captcha_sid'); ?>
					<?php echo $form->labelEx($model,'captcha_word'); ?>
					<?php echo $form->textField($model,'captcha_word'); ?>
					<?php echo $form->error($model,'captcha_word'); ?>
				</div>
			
			
				<div class="row buttons">
					<?php echo CHtml::submitButton($model->web_form_submit, Array('name'=>'web_form_submit')); ?>
				</div>
			</div>
		<?php $this->endWidget(); ?>
		
		</div><!-- form -->
	</div>
</div>