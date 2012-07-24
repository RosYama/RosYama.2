<?php
/**
 * ECommentsFormWidget class file.
 *
 * @author Dmitry Zasjadko <segoddnja@gmail.com>
 * @link https://github.com/segoddnja/ECommentable
 */

/**
 * Widget for view comments form for current model
 *
 * @version 1.0
 * @package Comments module
 */
Yii::import('comments.widgets.ECommentsBaseWidget');
class ECommentsFormWidget extends ECommentsBaseWidget
{       
        /**
         * Is used for display validation errors
         * @var Comment newComment 
         */
        public $validatedComment;
        
	public function run()
	{
            if($this->registeredOnly === false || Yii::app()->user->isGuest === false)
            {
                $this->render('ECommentsFormWidget', array(
                    'newComment' => $this->validatedComment ? $this->validatedComment : $this->createNewComment(),
                ));
            }
            else 
            {
                echo '<strong>'.Yii::t('CommentsModule.msg', 'You cannot add a new comment').'</strong>';
            }
	}
}
?>