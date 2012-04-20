<div id="userGroups-container">
	<?php if(Yii::app()->user->hasFlash('user')):?>
    <div class="info">
    <p>
		<font class="notetext">
        <?php echo Yii::app()->user->getFlash('user'); ?>
        </font>
    <p>    
    </div>
	<?php endif; ?>
	<?php if(Yii::app()->user->hasFlash('mail')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('mail'); ?>
    </div>
	<?php endif; ?>


	<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-groups-misc-form',
		'enableAjaxValidation'=>true,
		'enableClientValidation'=>true,
		'action'=>'/profile/update/',
		'htmlOptions'=>Array ('enctype'=>'multipart/form-data'),
	)); ?>
	<p class="note">Поля отмеченные <span class="required">*</span> являются обязательными.</p>
	<?php echo $form->errorSummary(Array($miscModel,$miscModel->relProfile)); ?>
	<table class="profileTable">
	<tbody>
		<tr>
		<td><?php echo $form->labelEx($miscModel,'name'); ?>
			<?php echo $form->textField($miscModel,'name',array('maxlength'=>50,'class'=>"textInput")); ?>
			<?php echo $form->error($miscModel,'name'); ?>
		</td>
		<td><?php echo $form->labelEx($miscModel,'second_name'); ?>
			<?php echo $form->textField($miscModel,'second_name',array('maxlength'=>50,'class'=>"textInput")); ?>
			<?php echo $form->error($miscModel,'second_name'); ?>
		</td>
		<td><?php echo $form->labelEx($miscModel,'last_name'); ?>
			<?php echo $form->textField($miscModel,'last_name',array('maxlength'=>50,'class'=>"textInput")); ?>
			<?php echo $form->error($miscModel,'last_name'); ?>
		</td>
	</tr>
	<tr>
		<td><?php echo $form->labelEx($miscModel,'email'); ?>
			<?php echo $form->textField($miscModel,'email',array('maxlength'=>50)); ?>
			<?php echo $form->error($miscModel,'email'); ?>
		</td>
		<td>
		<?php echo $form->labelEx($miscModel->relProfile,'image'); ?>
		<?php echo $form->fileField($miscModel->relProfile,'image',array('maxlength'=>50,'class'=>'typefile')); ?>
		<?php echo $form->error($miscModel->relProfile,'image'); ?>		
		</td> 
		
		<td rowspan=2>
		<?php if($miscModel->relProfile->avatar) echo CHtml::image($miscModel->relProfile->avatar_folder.'/'.$miscModel->relProfile->avatar); ?>
		</td>
	</tr>
	<tr>
		<td><?php echo $form->labelEx($miscModel->relProfile,'birthday'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model'=>$miscModel->relProfile,
			'attribute'=>'birthday',
			
		    // additional javascript options for the date picker plugin
		    'options'=>array(
		        'showAnim'=>'fold',
		        'dateFormat'=>'dd.mm.yy',
		        'changeYear'=>true,
		        'changeMonth'=>true,
		        'defaultDate'=>'-25y',
		        'showOtherMonths'=>true,
		        'selectOtherMonths'=>true,
		        
		    ),
		    'htmlOptions'=>array(
		    	'value'=>$miscModel->relProfile->getDateValue('birthday'),
		        //'id'=>'CurrentOffers_dates_from'.$type->id
		    ),
		));
		?>
		<?php //echo $form->textField($miscModel->relProfile,'birthday',array('maxlength'=>50,'class'=>'textInput')); ?>
		<?php echo $form->error($miscModel->relProfile,'birthday'); ?>
		<em>DD.MM.YYYY</em>
		</td>
		<td><?php echo $form->labelEx($miscModel->relProfile,'site'); ?>
		<?php echo $form->textField($miscModel->relProfile,'site',array('maxlength'=>50,'class'=>'textInput')); ?>
		<?php echo $form->error($miscModel->relProfile,'site'); ?></td>	
	</tr>
	<tr>
		<td colspan="2">
		<?php echo $form->labelEx($miscModel->relProfile,'aboutme'); ?>
		<?php echo $form->textArea($miscModel->relProfile,'aboutme',array('rows'=>10,'cols'=>10,'class'=>'textInput')); ?>
		<?php echo $form->error($miscModel->relProfile,'aboutme'); ?>
		</td>
		<td>
		<div class="f chekboxes">
		<?php echo $form->labelEx($miscModel,'params'); ?><br/>
		<?php echo $form->checkBoxList($miscModel,'params',$miscModel->paramsFields,Array('template'=>'{input}{label}')); ?>
		</div>
		</td>
		</tr>
		<tr>
		<td colspan="3">
		<?php echo $form->labelEx($miscModel->relProfile,'request_from'); ?>
		<?php echo $form->textField($miscModel->relProfile,'request_from',array('maxlength'=>255,'class'=>'textInput')); ?>
		<?php echo $form->error($miscModel->relProfile,'request_from'); ?>
		</td>
		</tr><tr>
		<td colspan="3">
		<?php echo $form->labelEx($miscModel->relProfile,'request_signature'); ?>
		<?php echo $form->textField($miscModel->relProfile,'request_signature',array('maxlength'=>100,'class'=>'textInput')); ?>
		<?php echo $form->error($miscModel->relProfile,'request_signature'); ?>
		</td>
		</tr>
		<tr>
		<td colspan="3">
		<?php echo $form->labelEx($miscModel->relProfile,'request_address'); ?>
		<?php echo $form->textField($miscModel->relProfile,'request_address',array('maxlength'=>255,'class'=>'textInput')); ?>
		<?php echo $form->error($miscModel->relProfile,'request_address'); ?>  
		</td>
		</tr>
	</tbody>
</table>	
<div class="row buttons">
			<?php echo CHtml::hiddenField('formID', $form->id) ?>
			<?php echo CHtml::submitButton('Обновить профиль'); ?>
			<?php //echo CHtml::ajaxSubmitButton(Yii::t('userGroupsModule.general','Update User Profile'), Yii::app()->baseUrl . '/profile/', array('update' => '#userGroups-container'), array('id' => 'submit-mail'.$passModel->id.rand()) ); ?>
		</div>
	<?php $this->endWidget(); ?>
	</div><!-- form -->


	<h2>Смена пароля</h2>
	<div class="form">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-groups-password-form',
		'enableAjaxValidation'=>true,
		'enableClientValidation'=>true,
	)); ?>

		<?php if ($passModel->xml_id && $passModel->external_auth_id) :?>
		<div class="row">
			<?php echo $form->labelEx($passModel,'username'); ?>
			<?php echo $form->textField($passModel,'username',array('size'=>60,'maxlength'=>120)); ?>
			<p>Изменив логин вы потеряете возможность авторизовываться через аккаунт социальной сети!</p>
			<?php echo $form->error($passModel,'username'); ?>
		</div>
		<?php endif; ?>
		<?php if (!$miscModel->password || (Yii::app()->user->pbac('userGroups.user.admin') && Yii::app()->user->id !== $passModel->id)) :?>
			<?php echo $form->hiddenField($passModel,'old_password', array('value'=>'filler'))?>
		<?php else: ?>		
		
		<div class="row">
			<?php echo $form->labelEx($passModel,'old_password'); ?>
			<?php echo $form->passwordField($passModel,'old_password',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'old_password'); ?>
		</div>
		<?php endif; ?>
		<div class="row">
			<?php echo $form->labelEx($passModel,'password'); ?>
			<?php echo $form->passwordField($passModel,'password',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'password'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($passModel,'password_confirm'); ?>
			<?php echo $form->passwordField($passModel,'password_confirm',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'password_confirm'); ?>
		</div>
		<?php if (UserGroupsConfiguration::findRule('simple_password_reset') === false): ?>
		<div class="row">
			<?php echo $form->labelEx($passModel,'question'); ?>
			<?php echo $form->textField($passModel,'question',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'question'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($passModel,'answer'); ?>
			<?php echo $form->passwordField($passModel,'answer',array('size'=>60,'maxlength'=>120)); ?>
			<?php echo $form->error($passModel,'answer'); ?>
		</div>
		<?php endif; ?>
		<div class="row buttons">
			<?php echo CHtml::hiddenField('formID', $form->id) ?>
			<?php echo CHtml::submitButton('Изменить пароль'); ?>
			<?php //echo CHtml::ajaxSubmitButton(Yii::t('userGroupsModule.general','Change Password'), Yii::app()->baseUrl .'/userGroups/user/update/id/'.$passModel->id, array('update' => '#userGroups-container'), array('id' => 'submit-pass'.$passModel->id.rand()) ); ?>
		</div>

	<?php $this->endWidget(); ?>
	</div><!-- form -->
</div>