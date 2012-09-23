<p>Автоматически сгенерированное информационное сообщение сайта
<?php echo CHtml::link(Yii::app()->name, 'http://'.$_SERVER['HTTP_HOST'].'/holes/index');?></p>
<p>------------------------------------------</p>

<h2>Здравствуйте, <?php echo $user->fullName; ?></h2>
<p><strong>Истек срок ожидания ответа от ГИБДД, на отправленные Вами запросы по дефектам:</strong></p>
<?php foreach ($holes as $i=>$hole) :?>
<?php $this->renderPartial("/holes/_view_in_mail", Array('data'=>$hole, 'index'=>$i, 'user'=>Yii::app()->user)); ?>
<?php endforeach; ?>
<p><strong>Если вы получили ответы на эти дефекты или они уже устранены, пожалуйста загрузите ответы либо отметьте факт исправления на сайте.</strong></p>

<br/>
<hr/>
<p>Не хотите получать такие уведомления? Отключите соответствующую опцию в <?php echo CHtml::link('настройках вашего профиля.', 'http://'.$_SERVER['HTTP_HOST'].'/profile/update');?></p>
