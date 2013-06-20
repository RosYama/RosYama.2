<?php
$this->pageTitle=Yii::app()->name . ' :: Обновление БД';
$this->breadcrumbs=array('Обновление БД');
?>
<blockquote>
	<?= $output ?>
</blockquote>
<form action="/site/dbupdate" method="post">
	<h1>Текущая версия таблиц</h1>
    <table>
		<? foreach($_version as $letter => $version): ?>
	        <tr>
	            <td><?= $letter === 0 ? 'default(0)' : htmlspecialchars($letter) ?>:</td>
	            <td><strong><?= (int)$version ?></strong></td>
	        </tr>
		<? endforeach; ?>
    </table><br>
	<h1>Доступная версия таблиц</h1>
    <table>
		<? foreach($_versionAvailable as $letter => $version): ?>
	        <tr>
	            <td><?= $letter === 0 ? 'default(0)' : htmlspecialchars($letter) ?>:</td>
	            <td><strong><?= (int)$version ?></strong></td>
	        </tr>
		<? endforeach; ?>
    </table><br>
    <input type="submit" name="submit" value="Обновить всё">
</form>