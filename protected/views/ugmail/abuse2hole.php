<h2>Новая жалоба на яму</h2>
<p>Пользователь <?php echo CHtml::link($user->Fullname, array('/profile/view', 'id'=>$user->id));?>, отправил жалобу на яму:</p>
<p><?php echo nl2br(CHtml::encode($abuse->text)); ?></p>
<?php $this->renderPartial("/holes/_view_in_mail", Array('data'=>$hole, 'index'=>0, 'user'=>$user)); ?>
<br/><br/><br/>


-----<br/>
<?php echo CHtml::link('РосЯма', array('/holes/index'));?>
