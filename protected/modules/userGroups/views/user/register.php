<?php
$this->breadcrumbs=array(
	Yii::t('userGroupsModule.general','User Registration'),
);
?>
<p>На указанный в форме e-mail придет запрос на подтверждение регистрации.</p>
<noindex>
<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-groups-passrequest-form',
			'enableAjaxValidation'=>false,
			'enableClientValidation'=>true,
		)); ?>

<table class="data-table bx-registration-table">
	<thead>
		<tr>
			<td colspan="2"><b>Регистрация</b></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $form->labelEx($model,'name'); ?></td>
			<td><?php echo $form->textField($model,'name'); ?>
			<?php echo $form->error($model,'name'); ?></td>
		</tr>
		<tr>
			<td><?php echo $form->labelEx($model,'last_name'); ?></td>
			<td><?php echo $form->textField($model,'last_name'); ?>
			<?php echo $form->error($model,'last_name'); ?></td>
		</tr>
		<tr>
			<td><?php echo $form->labelEx($model,'username'); ?></td>
			<td>			<?php echo $form->textField($model,'username'); ?>
			<?php echo $form->error($model,'username'); ?></td>
		</tr>
		<tr>
			<td><?php echo $form->labelEx($model,'password'); ?></td>
			<td>			<?php echo $form->passwordField($model,'password'); ?>
			<?php echo $form->error($model,'password'); ?></td>
		</tr>
		<tr>
			<td><?php echo $form->labelEx($model,'password_confirm'); ?></td>
			<td><?php echo $form->passwordField($model,'password_confirm'); ?>
			<?php echo $form->error($model,'password_confirm'); ?></td>
		</tr>
		<tr>
			<td><?php echo $form->labelEx($model,'email'); ?></td>
			<td><?php echo $form->textField($model,'email'); ?>
			<?php echo $form->error($model,'email'); ?></td>
		</tr>
		<tr>
			<td colspan="2"><b>Защита от автоматической регистрации</b></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php $this->widget('CCaptcha', array(
				'clickableImage'=>true,
				'buttonOptions'=>array(
					'id'=>'refreshCaptcha',
				),
			)); ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $form->labelEx($model,'captcha'); ?></td>
			<td><?php echo $form->textField($model,'captcha'); ?>
			<?php echo $form->error($model,'captcha'); ?></td>
		</tr>
			</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td><?php echo CHtml::submitButton('Регистрация'); ?></td>
		</tr>
	</tfoot>
</table>
<p>Пароль должен быть не менее 6 символов длиной.</p>
<p><span class="required">*</span>Обязательные поля</p>

<p>
<a href="/personal/holes.php?login=yes" rel="nofollow"><b>Авторизация</b></a>
</p>

<?php $this->endWidget(); ?>
</noindex>
	</div>