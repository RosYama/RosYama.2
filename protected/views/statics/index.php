<?
$this->pageTitle=Yii::app()->name . ' - Статистика';
$this->title='Статистика';
?>

<div class="lCol">
<?php $this->widget('application.widgets.social.socialWidget'); ?>
</div>
<div class="rCol">
<h2><?=Yii::t('statics', 'PIT_SITY')?>:</h2>
<div class="stats">
	<?foreach($arResult['geography'][0] as $ar){?>
		<?php echo CHtml::link(CHtml::encode(trim($ar['ADR_CITY'])),Array('/holes/index','Holes[ADR_CITY]'=>trim($ar['ADR_CITY']))); ?> &nbsp; &mdash;  <?=$ar['counts']?><br>
	<?}?>
</div>

<h2><?=Yii::t('statics', 'FIXED_PIT_SITY')?>:</h2>
<div class="stats">
<?foreach($arResult['geography'][1] as $ar){?>
	<?php echo CHtml::link(CHtml::encode(trim($ar['ADR_CITY'])),Array('/holes/index','Holes[ADR_CITY]'=>trim($ar['ADR_CITY']),'Holes[STATE]'=>'fixed')); ?>&nbsp; &mdash;  <?=$ar['counts']?><br>
<?}?>
</div>

<h2><?=Yii::t('statics', 'PIT_STATE')?>:</h2>
<div class="stats">
<?foreach($arResult['STATE'][0] as $ar){?>
	<?php echo CHtml::link(CHtml::encode($ar['STATE']),Array('/holes/index','Holes[STATE]'=>$ar['state_to_filter'])); ?>&nbsp; &mdash;  <?=$ar['counts']?><br>
<?}?>
</div>

<h2><?=Yii::t('statics', 'AGV_TIME')?>:</h2>
<div class="stats">
<?foreach($arResult['STATE'][1] as $ar){?>
	<?=$ar['time']?><br>
<?}?>
</div>

<h2><?=Yii::t('statics', 'PIT_PEOPLES')?>:</h2>
<div class="stats">
<?foreach($arResult['user'][0] as $ar){?>
	<?php echo $ar['user']; ?> &nbsp; &mdash;  <?=$ar['counts']?><br>
<?}?>
</div>

<h2><?=Yii::t('statics', 'FIXED_PIT_PEOPLES')?>:</h2>
<div class="stats">
<?foreach($arResult['user'][1] as $ar){?>
	<?php echo $ar['user']; ?> &nbsp; &mdash;  <?=$ar['counts']?><br>
<?}?>
</div>

<?php if (Yii::app()->user->level >= 90 && $arResult['moders']) : ?>
<h2><?=Yii::t('statics', 'TOP_MODERS')?>:</h2>
	<div class="stats">
<?foreach($arResult['moders'] as $ar){?>
	<?php echo $ar['moder']; ?> &nbsp; &mdash;  <?=$ar['counts']?><br>
<?}?>
</div>	
<?php endif; ?>
</div>
