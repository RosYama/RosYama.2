<?php
/**
 * ECommentsListWidget class file.
 *
 * @author Dmitry Zasjadko <segoddnja@gmail.com>
 * @link https://github.com/segoddnja/ECommentable
 */

/**
 * Widget for view comments for current model
 *
 * @version 1.0
 * @package Comments module
 */
Yii::import('comments.widgets.ECommentsBaseWidget');
class ECommentsListWidget extends ECommentsBaseWidget
{       
        /**
         * @var boolean showPopupForm
         */
        public $showPopupForm = true;
        
        /**
         * @var boolean allowSubcommenting
         */
        public $allowSubcommenting = true;
        
        /**
         * @var boolean adminMode
         */
        public $adminMode = false;
        
        /**
         * Initializes the widget.
         */
        public function init() 
        {
            parent::init();
            if(count($this->_config) > 0)
            {
                $this->allowSubcommenting = isset($this->_config['allowSubcommenting']) ? $this->_config['allowSubcommenting'] : $this->allowSubcommenting;
                if($this->_config['isSuperuser'] !== '')
                    $this->adminMode = $this->evaluateExpression($this->_config['isSuperuser']);
            }
        }
        
	public function run()
	{
            $newComment = $this->createNewComment();
            $comments = $newComment->getCommentsTree();
            $this->render('ECommentsListWidget', array(
                'comments' => $comments,
                'newComment' => $newComment,
            ));
            $options = CJavaScript::encode(array(
                'dialogTitle' => Yii::t('CommentsModule.msg', 'Add comment'),
                'deleteConfirmString' => Yii::t('CommentsModule.msg', 'Delete this comment?'),
                'approveConfirmString' => Yii::t('CommentsModule.msg', 'Approve this comment?'),
                'postButton' => Yii::t('CommentsModule.msg', 'Add comment'),
                'cancelButton' => Yii::t('CommentsModule.msg', 'Cancel'),
            ));
            $js = "jQuery('#{$this->id}').commentsList($options);";
            Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id, $js);
	}
}
?>
