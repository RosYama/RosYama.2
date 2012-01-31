  <div class="head">
		<div class="container">
			<div class="lCol"><a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
			</div>
				<div class="rCol">
								
					<div id="head_user_info">				
					<div class="photo">
						<?php if($model->relProfile && $model->relProfile->avatar) echo CHtml::image($model->relProfile->avatar_folder.'/'.$model->relProfile->avatar); ?>
					</div>
					<div class="info">		
						<h1><?php if($model->getParam('showFullname')) echo $model->fullName; elseif($model->name) echo $model->name; else echo $model->username; ?></h1>
						<div class="www">
							<a target="_blank" href="http://"></a>
						</div>
					</div>
					<div class="counter">
						<?php echo Y::declOfNum($model->holes_cnt, array('дефект', 'дефекта', 'дефектов')); ?> / <?php echo Y::declOfNum($model->holes_fixed_cnt, array('отремонтирован', 'отремонтировано', 'отремонтировано')); ?>						
					</div>	
				</div>		
			</div>
		</div>
	</div>
<div class="mainCols">
	<div class="lCol">
	<?php if($model->hole_area && $model->getParam('showMyarea')) : ?>
	<h2>Зона наблюдения</h2>
	<?php $this->widget('application.widgets.userAreaMap.userAreaMapWidget',Array('data'=>Array('area'=>$model->hole_area))); ?>
	<?php endif; ?>
	
	<?php if($model->relProfile && $model->relProfile->aboutme && $model->getParam('showAboutme')) : ?>
	
	<h2>Обо мне</h2>
	<p><?php echo nl2br($model->relProfile->aboutme); ?></p>
	<?php endif; ?>
	</div>
	<div class="rCol">
	<?php if($model->getParam('showContactForm')) : ?>
		<?php if(Yii::app()->user->hasFlash('contact')): ?>
	
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('contact'); ?>
		</div>
		
		<?php else: ?>
		<h2>Отправить сообщение</h2>
			<div class="form">
		
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'contact-form',
			'enableClientValidation'=>true,
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
		)); ?>
		
			<p class="note">Поля отмеченные <span class="required">*</span> являются обязательными.</p>
		
			<?php echo $form->errorSummary($contactModel); ?>	
		
			<div class="row">
				<?php echo $form->labelEx($contactModel,'subject'); ?>
				<?php echo $form->textField($contactModel,'subject',array('size'=>60,'maxlength'=>128)); ?>
				<?php echo $form->error($contactModel,'subject'); ?>
			</div>
		
			<div class="row">
				<?php echo $form->labelEx($contactModel,'body'); ?>
				<?php echo $form->textArea($contactModel,'body',array('rows'=>10, 'cols'=>60)); ?>
				<?php echo $form->error($contactModel,'body'); ?>
			</div>
			
		
			<div class="row buttons">
				<?php echo CHtml::submitButton('Отправить'); ?>
			</div>
		
		<?php $this->endWidget(); ?>
		
		</div><!-- form -->
		<?php endif; ?>
	<?php endif; ?>	
	</div>
</div>		
	
