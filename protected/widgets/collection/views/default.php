<p class="collection">
	Наша коллекция насчитывает<br /><strong><a href="/map/"><?php echo $all; ?></a> / <a href="/map/?STATE[2]=inprogress"><?php echo $ingibdd; ?>&nbsp; в гибдд</a> / <a href="/map/?STATE[3]=fixed"><?php echo $fixed; ?>&nbsp;исправлено</a> / <?php echo CHtml::link($archive.'&nbsp;в архиве', Array('/holes/index','Holes[archive]'=>1)); ?></strong>
</p>