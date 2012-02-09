<div id="userGroups-container">
	<?php if(isset(Yii::app()->request->cookies['success'])): ?>
	<div class="info">
		<?php echo Yii::app()->request->cookies['success']->value; ?>
		<?php unset(Yii::app()->request->cookies['success']);?>
	</div>
	<?php endif; ?>
	<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
	<?php endif; ?>
	<?php if(Yii::app()->user->hasFlash('mail')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('mail'); ?>
    </div>
	<?php endif; ?>
	<div class="form center">
	
	</div><!-- form -->
</div>
<div class="bx-auth">
	<div class="bx-auth-title">Войти на сайт</div>
	<div class="bx-auth-note">Пожалуйста, авторизуйтесь:</div>

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'login-form',
		'enableAjaxValidation'=>false,
		'focus'=>array($model, 'username'),
	)); ?>
	
	<table class="bx-auth-table">
			<tr>
				<td class="bx-auth-label"><?php echo $form->labelEx($model,'username'); ?>:</td>
				<td><?php echo $form->textField($model,'username'); ?>
					<?php echo $form->error($model,'username'); ?>
				</td>
			</tr>
			<tr>
				<td class="bx-auth-label"><?php echo $form->labelEx($model,'password'); ?>:</td>
				<td><?php echo $form->passwordField($model,'password'); ?>
					<?php echo $form->error($model,'password'); ?>
				</td>
			</tr>
						<tr>
				<td></td>
				<td><?php echo $form->checkBox($model,'rememberMe'); ?>
					<?php echo $form->label($model,'rememberMe'); ?>
					<?php echo $form->error($model,'rememberMe'); ?>
			</td>
			</tr>
			<tr>
				<td></td>
				<td class="authorize-submit-cell"><?php echo CHtml::submitButton('Войти'); ?></td>
			</tr>
		</table>
		
		<noindex>
			<p>
				<a href="/personal/holes.php?forgot_password=yes" rel="nofollow">Забыли свой пароль?</a>
			</p>
		</noindex>
		
		<?php if (UserGroupsConfiguration::findRule('registration')): ?>
		<noindex>
			<p>
				<?php echo CHtml::link('Зарегистрироваться', array('/userGroups/user/register'))?><br />
				Если вы впервые на сайте, заполните, пожалуйста, регистрационную форму. 
			</p>
		</noindex>
		<?php endif; ?>		
				
	
	<?php $this->endWidget(); ?>	
	
</div>

<script type="text/javascript">
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
</script>

<div class="bx-auth-title">Войти как пользователь</div>
<div class="bx-auth-note">Вы можете войти на сайт, если вы зарегистрированы на одном из этих сервисов:</div>
<?php $this->widget('ext.eauth.EAuthWidget', array('action' => '/userGroups/')); ?>

