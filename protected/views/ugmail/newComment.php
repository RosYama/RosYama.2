<h2>Здравствуйте, <?php echo $user->fullName; ?></h2>
<p>Пользователь <?php echo CHtml::link($comment->user->Fullname, 'http://'.$_SERVER['HTTP_HOST'].'/profile/view/id/'.$comment->user->id);?>, оставил комментарий к вашему дефекту на сайте <?php echo CHtml::link(Yii::app()->name, 'http://'.$_SERVER['HTTP_HOST'].'/holes/index');?></p>
<h3>Текст комментария:</h3>
<p><?php echo nl2br(CHtml::encode($comment->comment_text)); ?></p>
<p>Ссылка: <?php echo CHtml::link('http://'.$_SERVER['HTTP_HOST'].$comment->pageUrl, 'http://'.$_SERVER['HTTP_HOST'].$comment->pageUrl); ?></p>

<br/>
<hr/>
<p>Не хотите получать такие уведомления? Отключите соответствующую опцию в <?php echo CHtml::link('настройках вашего профиля.', 'http://'.$_SERVER['HTTP_HOST'].'/profile/update');?></p>
