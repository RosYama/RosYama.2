<?php
/**
 * ECommentsBaseWidget class file.
 *
 * @author Dmitry Zasjadko <segoddnja@gmail.com>
 * @link https://github.com/segoddnja/ECommentable
 */

/**
 * Base class for allmodule widgets
 *
 * @version 1.0
 * @package Comments module
 */
Yii::import('zii.widgets.jui.CJuiWidget');

class ECommentsBaseWidget extends CJuiWidget
{       
        /**
         * @var model for displaying comments
         */
        public $model;
        
        
        /**
         * If only registered users can post comments
         * @var registeredOnly
         */
        public $registeredOnly = false;
        
        /**
         * Use captcha validation on posting
         * @var registeredOnly
         */
        public $useCaptcha = false;
        
        /**
         * Action for posting comments, where add comment form is submited
         * @var postCommentAction
         */
        public $postCommentAction = 'comments/comment/postComment';
        
        /**
         * @var array
         */
        protected $_config;
        
        /**
         * Initializes the widget.
         */
        public function init() 
        {
            parent::init();
            //get comments module
            $commentsModule = Yii::app()->getModule('comments');
            //get model config for comments module
            $this->_config = $commentsModule->getModelConfig($this->model);
            if(count($this->_config) > 0)
            {
                $this->registeredOnly = isset($this->_config['registeredOnly']) ? $this->_config['registeredOnly'] : $this->registeredOnly;
                $this->useCaptcha = isset($this->_config['useCaptcha']) ? $this->_config['useCaptcha'] : $this->useCaptcha;
                $this->postCommentAction = isset($this->_config['postCommentAction']) ? $this->_config['postCommentAction'] : $this->postCommentAction;
            }
            $this->registerScripts();
        }

        /**
         * Registers the JS and CSS Files
         */
        protected function registerScripts() 
        {
            $assets = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('comments') . '/assets');
            $cs = Yii::app()->getClientScript();
            $cs->registerCssFile($assets . '/comments.css?'.time());
            $cs->registerScriptFile($assets . '/comments.js?'.time());
        }
        
        /*
         * Create new comment model and initialize it with owner data
         * @return EComments comment
         */
        protected function createNewComment()
        {
            $comment = new Comment();
            $comment->owner_name = get_class($this->model);
            $comment->owner_id = $this->getOwnerPK();
            return $comment;
        }
        
        /*
         * Return model PK, converted to string
         * @return String
         */
        protected function getOwnerPK(){
            if(is_array($this->model->primaryKey))
                return implode ('.', $this->model->primaryKey);
            else
                return $this->model->primaryKey;
        }
}
?>
