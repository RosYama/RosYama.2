<?php $this->beginContent('//layouts/main'); ?>
<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>
  <div class="head">
		<div class="container">
<div class="lCol"><a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
</div>
						<div class="rCol">
					
							<div id="head_user_info">
<div class="buttons">
		<?php $this->widget('zii.widgets.CMenu', array(
				'items'=>Array(
						array('label'=>'Добавить дефект', 'url'=>array('/holes/add'), 'linkOptions'=>array('class'=>'profileBtn')),
						array('label'=>'Мои ямы', 'url'=>array('/holes/personal'), 'linkOptions'=>array('class'=>'profileBtn')),
						array('label'=>'Мой участок', 'url'=>array('/holes/myarea'), 'linkOptions'=>array('class'=>'profileBtn')),
						array('label'=>'Изменить личные данные', 'url'=>array('/profile/update'), 'linkOptions'=>array('class'=>'profileBtn')),
						
					),
				'htmlOptions'=>array('class'=>'usermenu'),
			));?>
		</div>							
	<div class="photo">
		<?php if(Yii::app()->user->userModel->relProfile && Yii::app()->user->userModel->relProfile->avatar) echo CHtml::image(Yii::app()->user->userModel->relProfile->avatar_folder.'/'.Yii::app()->user->userModel->relProfile->avatar); ?>
	</div>
	<div class="info">		
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
						array('label'=>'Новости', 'url'=>array('/news/admin'), 'linkOptions'=>array('class'=>'profileBtn')),
						array('label'=>'Пользователи', 'url'=>array('/userGroups/'), 'linkOptions'=>array('class'=>'profileBtn')),
						array('label'=>'Типы ям', 'url'=>array('/holeTypes/index'), 'linkOptions'=>array('class'=>'profileBtn')),
						array('label'=>'Результаты запроса в ГИБДД (анкета)', 'url'=>array('/holeAnswerResults/index'), 'linkOptions'=>array('class'=>'profileBtn')),
					),
				'htmlOptions'=>array('class'=>'operations'),
			));			
		}
	if ($this->menu) {
			echo '<br/><br/>';
			$this->beginWidget('zii.widgets.CPortlet', array(				
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