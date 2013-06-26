<?php
$this->pageTitle=Yii::app()->name . ' :: Обновление БД';
$this->breadcrumbs=array('Обновление БД');
?>
<blockquote>
	<?php echo $output ?>
</blockquote>
<form action="/site/dbupdate" method="post">
	<h1>Текущая версия таблиц</h1>
    <table>
		<?php foreach($_version as $letter => $version): ?>
	        <tr>
	            <td><?php echo $letter === 0 ? 'default(0)' : htmlspecialchars($letter) ?>:</td>
	            <td><strong><?php echo (int)$version ?></strong></td>
	        </tr>
		<? endforeach; ?>
    </table><br>
	<h1>Доступная версия таблиц</h1>
    <table>
		<? foreach($_versionAvailable as $letter => $version): ?>
	        <tr>
	            <td><?php echo $letter === 0 ? 'default(0)' : htmlspecialchars($letter) ?>:</td>
	            <td><strong><?php echo (int)$version ?></strong></td>
	        </tr>
		<? endforeach; ?>
    </table><br>
    <input type="submit" name="submit" value="Обновить всё">
</form>