<?php $this->pageTitle=Yii::app()->name; ?>

<div class="form login">
<?php $form=$this->beginWidget('CActiveForm', array(
	'focus' => array($model, 'root_user'),
	'enableAjaxValidation'=>true 
)); ?>
	<p>Enter the username and password for the root user of your application.</p>
	<p>The root user has access to every page of the application, his permissions cannot be changed and cannot be banned.</p>
	<p>You can have just one root user, however you can create other users or groups and assign them the same permissions of the root user.</p>
	
	<div>
		<?php echo $form->label($model, 'root_user') ?>	
		<?php echo $form->textField($model,'root_user'); ?>
		<?php echo $form->error($model,'root_user'); ?>
	</div>
	<div>
		<?php echo $form->label($model, 'root_password') ?>
		<?php echo $form->passwordField($model,'root_password'); ?>
		<?php echo $form->error($model,'root_password'); ?>
	</div>
	<div>
		<?php echo $form->label($model, 'root_password_confirm') ?>
		<?php echo $form->passwordField($model,'root_password_confirm'); ?>
		<?php echo $form->error($model,'root_password_confirm'); ?>
	</div>
	<div>
		<?php echo $form->label($model, 'root_email') ?>
		<?php echo $form->textField($model,'root_email'); ?>
		<?php echo $form->error($model,'root_email'); ?>
	</div>
	<div>
		<?php echo $form->label($model, 'root_question') ?>
		<?php echo $form->textField($model,'root_question'); ?>
		<?php echo $form->error($model,'root_question'); ?>
	</div>
	<div>
		<?php echo $form->label($model, 'root_answer') ?>
		<?php echo $form->textField($model,'root_answer'); ?>
		<?php echo $form->error($model,'root_answer'); ?>
	</div>
	<div>
		<?php echo CHtml::hiddenField('action', 'installation'); ?>
		<?php echo CHtml::submitButton('Install'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->