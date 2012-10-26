<?php if(count($comments) > 0):?>
    <ul class="comments-list">
        <?php foreach($comments as $comment):?>
            <li id="comment-<?php echo $comment->comment_id; ?>">
            	<?php if ($comment->status!=$comment::STATUS_DELETED) : ?>
            	<div class="comment-avatar">
            	<?php if($comment->user->relProfile && $comment->user->relProfile->avatar) echo CHtml::image($comment->user->relProfile->avatar_folder.'/'.$comment->user->relProfile->avatar); 
            			else echo CHtml::image('/images/userpic-user.png');
            	?>
            	</div>
                <div class="comment-header">	
                    <?php echo CHtml::link($comment->userName, Array('/profile/view', 'id'=>$comment->user->id));?>
                    <?php echo Yii::app()->dateFormatter->formatDateTime($comment->create_time);?>
                     
                    <span class="admin-panel">
                    <?php /* if($this->adminMode === true):?>
                        <?php if($comment->status === null || $comment->status == Comment::STATUS_NOT_APPROWED) echo CHtml::link(Yii::t('CommentsModule.msg', 'approve'), Yii::app()->urlManager->createUrl(
                            CommentsModule::APPROVE_ACTION_ROUTE, array('id'=>$comment->comment_id)
                        ), array('class'=>'approve'));?>
                    <?php endif;*/ ?>    
                        <?php if (Yii::app()->user->id == $comment->user->id || Yii::app()->user->level > 80 || $this->model->IsUserHole) echo CHtml::link(Yii::t('CommentsModule.msg', 'delete'), Yii::app()->urlManager->createUrl(
                            CommentsModule::DELETE_ACTION_ROUTE, array('id'=>$comment->comment_id)
                        ), array('class'=>'delete'));?>
                    </span>
                
                </div>
               
                <div class="comment-text">
                     <?php echo nl2br(CHtml::encode($comment->comment_text));?>
                                     <?php
                    if($this->allowSubcommenting === true && ($this->registeredOnly === false || Yii::app()->user->isGuest === false))
                    {
                        echo '<br/><br/>'.CHtml::link(Yii::t('CommentsModule.msg', 'Answer comment'), '#', array('rel'=>$comment->comment_id, 'class'=>'add-comment'));
                    }
                ?>
                </div>
                
             <?php else : ?>
 			  	<div class="comment-header">
 			  		<?php echo Yii::t('CommentsModule.msg', 'Comment deleted'); ?>
 			  	</div>
              <?php endif; ?> 
                
                <div class="clear"></div>
                <?php if(count($comment->childs) > 0 && $this->allowSubcommenting === true) $this->render('ECommentsWidgetComments', array('comments' => $comment->childs));?>
 
            </li>
        <?php endforeach;?>
    </ul>
<?php else:?>
    <p><?php echo Yii::t('CommentsModule.msg', 'No comments');?></p>
<?php endif; ?>

