<p>Автоматически сгенерированное информационное сообщение сайта
<?php echo CHtml::link(Yii::app()->name, 'http://'.$_SERVER['HTTP_HOST'].'/holes/index');?></p>
<p>------------------------------------------</p>

<h2>Здравствуйте, <?php echo $user->fullName; ?></h2>
<p><strong>Пользователь <?php echo $currentUser->fullName; ?>, загрузил фотографии исправленного дефекта:</strong></p>
	<?php foreach($pictures as $i=>$picture): ?>
			<?php echo CHtml::image(Yii::app()->request->baseUrl.$picture->small); ?>
			<?php endforeach; ?>
<p><strong>К дефекту:</strong></p>
<?php $this->renderPartial("/holes/_view_in_mail", Array('data'=>$hole, 'index'=>$i, 'user'=>$user)); ?>
<p><strong>Если фотография соответствует действительности, подтвердите ее на странице дефекта.</strong></p>

<br/>
<hr/>
<p>Не хотите получать такие уведомления? Отключите соответствующую опцию в <?php echo CHtml::link('настройках вашего профиля.', 'http://'.$_SERVER['HTTP_HOST'].'/profile/update');?></p>
