<h2>Здравствуйте, <?php echo $user->fullName; ?></h2>
<p>Пользователь <?php echo CHtml::link($comment->user->Fullname, array('/profile/view', 'id'=>$comment->user->id));?>, оставил комментарий к вашей яме:</p>
<p><?php echo nl2br(CHtml::encode($comment->comment_text)); ?></p>
<p>Ссылка: <?php echo CHtml::link('http://'.$_SERVER['HTTP_HOST'].$comment->pageUrl, $comment->pageUrl); ?></p>
<br/><br/><br/>


-----<br/>
<?php echo CHtml::link('РосЯма', array('/holes/index'));?>
