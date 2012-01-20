<?php $this->beginContent('//layouts/main'); ?>
<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>
  <div class="head">
		<div class="container">
<div class="lCol"><a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
</div>
						<div class="rCol">
							<div id="head_user_info">
	<div class="photo">
		<?php if(Yii::app()->user->userModel->relProfile->avatar) echo CHtml::image(Yii::app()->user->userModel->relProfile->avatar_folder.'/'.Yii::app()->user->userModel->relProfile->avatar); ?>
	</div>
	<div class="info">
		<div class="buttons">
		<?php echo CHtml::link('Добавить дефект', array('/holes/add'), array('class'=>'profileBtn')); ?>
		<?php echo CHtml::link('Изменить личные данные', array('/profile/index'), array('class'=>'profileBtn')); ?>
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
	echo '<br/>';
	$this->widget('zii.widgets.CMenu', array(
				'items'=>Array(
						array('label'=>'Пользователи', 'url'=>array('/userGroups/')),
						array('label'=>'Типы ям', 'url'=>array('/holeTypes/index')),
						array('label'=>'Результаты запроса в ГИБДД (анкета)', 'url'=>array('/holeAnswerResults/index')),
					),
				'htmlOptions'=>array('class'=>'operations'),
			));
			echo '<br/>';
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