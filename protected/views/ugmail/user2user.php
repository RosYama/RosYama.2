<h2>Здравствуйте, <?php echo $touser->fullName; ?></h2>
<p>Пользователь <?php echo CHtml::link($fromuser->Fullname, array('/profile/view', 'id'=>$fromuser->id));?>, отправил вам сообщение:</p>
<p><?php echo nl2br($model->body); ?></p>
<br/>
-----<br/>
<?php echo CHtml::link('РосЯма', array('/holes/index'));?>
