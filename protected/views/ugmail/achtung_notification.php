<h2>Здравствуйте, <?php echo $user->fullName; ?></h2>
<p><strong>Истек срок ожидания ответа от ГИБДД, на отправленные Вами запросы по ямам:</strong></p>
<?php foreach ($holes as $i=>$hole) :?>
<?php $this->renderPartial("/holes/_view_in_mail", Array('data'=>$hole, 'index'=>$i, 'user'=>Yii::app()->user)); ?>
<?php endforeach; ?>

<p><strong>Если вы получили ответы на эти ямы или дефекты уже устранены, пожалуйста загрузите ответы либо отметьте факт исправления на сайте.</strong></p>

-----<br/>
<?php echo CHtml::link('РосЯма', 'http://'.$_SERVER['HTTP_HOST'].'/holes/index');?>
<br /><br />
Не хотите получать такие уведомления? Отключите соответствующую опцию в <?php echo CHtml::link('настройках вашего профиля', 'http://'.$_SERVER['HTTP_HOST'].'/profile/update');?>