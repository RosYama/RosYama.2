<?
$this->pageTitle=Yii::app()->name . ' - Статистика по периодам';
$this->title=CHtml::link('Статистика', Array('index')).' > по периодам';
?>

<div class="lCol">
<?php $this->widget('application.widgets.social.socialWidget'); ?>
</div>
<div class="rCol">
<?php for($y=date('Y'); $y>=date('Y', $firstDate); $y--) : ?>
<h2><?php echo $y; ?>:</h2>
<div class="stats">
	<table class="period_stats">
	<tr>
		<th>Месяц</th><th>Ям добавлено</th><th>Заявлений отправлено</th><th>Ям исправлено</th><th>Пользователей зарегистрировано</th>			
	</tr>

	<?php for ($m=1; $m<=12; $m++) : ?>
		<?php $ym=$m < 10 ? $y.'0'.$m : $y.$m;?>
		<?php if ($ym >= date('Ym', $firstDate) && $ym <= date('Ym')) : ?>	
		<tr>
			<td><?php echo Yii::t('statics', 'month'.$m); ?></td>
			<td><?php echo isset($result[$ym]['created']) ? $result[$ym]['created'] : 0; ?></td>	
			<td><?php echo isset($result[$ym]['sent']) ? $result[$ym]['sent'] : 0; ?></td>
			<td><?php echo isset($result[$ym]['fixed']) ? $result[$ym]['fixed'] : 0; ?></td>
			<td><?php echo isset($result[$ym]['users']) ? $result[$ym]['users'] : 0; ?></td>
		</tr>
		<?php endif; ?>
	
	<?php endfor; ?>
	
	</table>
</div>
<?php endfor; ?>


</div>
