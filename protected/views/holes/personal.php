<?
$this->pageTitle=Yii::app()->name . ' :: Мои ямы';
?>
<div class="lCol">


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
			'index'=>$i
		));?>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php endforeach; ?>

</div>


