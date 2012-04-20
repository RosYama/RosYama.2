<?
$this->pageTitle=Yii::app()->name . ' :: Мой участок';
?>

<?php $this->menu=array(
	Array('label'=>'Изменить границы моего участка', 'url'=>array('/profile/myarea'), 'linkOptions'=>array('class'=>'profileBtn')),
); ?>

<div class="lCol">

<?php $this->widget('application.widgets.userAreaMap.userAreaMapWidget',Array('data'=>Array('area'=>$area))); ?>

<?php if ($user->userModel->areaNeighbors) : ?>
<h3>Соседи:</h3>
<ul>
<?php foreach ($user->userModel->areaNeighbors as $neighbor) : ?>
	<li><?php echo CHtml::link(CHtml::encode($neighbor->getParam('showFullname') ? $neighbor->Fullname : $neighbor->username), array('/profile/view', 'id'=>$neighbor->id),array('class'=>""));?></li>
<?php endforeach; ?>
</ul>	
<?php endif; ?>

</div>

<div class="rCol">

<?php foreach ($model->AllstatesMany as $state_alias=>$state_name) : ?>
<?php  if ($holes[$state_alias]) : ?>
	<h2><?php echo $state_name; ?></h2>
	<ul class="holes_list">
	<?php foreach($holes[$state_alias] as $i=>$item) : ?>
		<?php 
		$this->renderPartial('_view',array(
			'data'=>$item,
			'index'=>$i,
			'user'=>Yii::app()->user,
		));?>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php endforeach; ?>

</div>


