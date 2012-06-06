<div class="comment-widget" id="<?php echo $this->id?>"><a name="comments"></a>
<h3><?php echo Yii::t('CommentsModule.msg', 'Comments');?></h3>
<?php
    $this->render('ECommentsWidgetComments', array('comments' => $comments));
    if($this->showPopupForm === true)
    {
        if($this->registeredOnly === false || Yii::app()->user->isGuest === false)
        {
            echo "<div id=\"addCommentDialog-$this->id\">";
                $this->widget('comments.widgets.ECommentsFormWidget', array(
                    'model' => $this->model,
                ));
            echo "</div>";
        }
    }
    if($this->registeredOnly === false || Yii::app()->user->isGuest === false)
    {
        echo CHtml::link(Yii::t('CommentsModule.msg', 'Add comment'), '#', array('rel'=>0, 'class'=>'add-comment'));
    }
    else 
    {
        echo '<strong>'.CHtml::link(Yii::t('CommentsModule.msg', 'You cannot add a new comment'), array('review','id'=>$this->model->ID),array('class'=>"declarationBtn")).'</strong>';
    }
?>
</div>
