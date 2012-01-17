<?php $this->beginContent('//layouts/main'); ?>
<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>
  <div class="head">
		<div class="container">
<div class="lCol"><a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
</div>
						<div class="rCol">
							<div id="head_user_info">
	<div class="photo">
			</div>
	<div class="info">
		<div class="buttons">
		<?php echo CHtml::link('Добавить дефект', array('add'), array('class'=>'profileBtn')); ?>
		<?php echo CHtml::link('Изменить личные данные', array('/personal/profile'), array('class'=>'profileBtn')); ?>
		</div>
		<h1><?php echo Yii::app()->user->fullName; ?></h1>
		<div class="www">
			<a target="_blank" href="http://"></a>
		</div>
	</div>
	<div class="counter">
		<?php echo Y::declOfNum(Yii::app()->user->usermodel->holes_cnt, array('дефект', 'дефекта', 'дефектов')); ?> / <?php echo Y::declOfNum(Yii::app()->user->usermodel->holes_fixed_cnt, array('отремонтирован', 'отремонтировано', 'отремонтировано')); ?>		
		
	</div>
	<?php if (Yii::app()->user->isAdmin) {
			$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>'Действия',
			));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'operations'),
			));
			$this->endWidget();
		}
		?>
</div>		
						</div>
		</div>
	</div>
	<div class="mainCols">
	<?php echo $content; ?>
	</div>		
	
<?php $this->endContent(); ?>