<?php $this->pageTitle=Yii::app()->name; ?>

<div class="form login">
<?php $form=$this->beginWidget('CActiveForm', array(
	'focus' => array($model, 'root_user'), 
)); ?>
	<p>Inserisci il nome utente e la password per l'utente di root della tua applicazione.</p>
	<p>L'utente di root ha accesso a tutte le pagine della tua applicazione, i suoi permessi non possono essere in alcun modo modificati e non pu&ograve; essere bannato.</p>
	<p>Pu&ograve; esserci un solo utente root, tuttavia puoi creare altri gruppi e/o utente e dargli permessi uguali a quelli di root.</p>
	
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
		<?php echo CHtml::submitButton('Installa'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->