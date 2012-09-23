<h2>Жалоба на дефект</h2>
<p>Пользователь <?php echo CHtml::link($user->Fullname, array('/profile/view', 'id'=>$user->id));?>, отправил жалобу на дефект.</p>
<h3>Текст жалобы:</h3>
<p><?php echo nl2br(CHtml::encode($abuse->text)); ?></p>
<p>Информация о дефекте:</p>
<?php $this->renderPartial("/holes/_view_in_mail", Array('data'=>$hole, 'index'=>0, 'user'=>$user)); ?>

<br/>
<hr/>
<p>Не хотите получать такие уведомления? Отключите соответствующую опцию в <?php echo CHtml::link('настройках вашего профиля.', 'http://'.$_SERVER['HTTP_HOST'].'/profile/update');?></p>
