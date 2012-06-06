<?php
/**
* Comments module class file.
*
* @author Dmitry Zasjadko <segoddnja@gmail.com>
* @link https://github.com/segoddnja/ECommentable
* @version 1.0
* @package Comments module
* 
*/
class CommentsModule extends CWebModule
{       
        public $defaultController = 'comment';
        
        /*
         * captcha action route
         */
        const CAPTCHA_ACTION_ROUTE = 'comments/comment/captcha';
        
        /*
         * delete comment action route
         */
        const DELETE_ACTION_ROUTE = 'comments/comment/delete';
        
        /*
         * approve comment action route
         */
        const APPROVE_ACTION_ROUTE = 'comments/comment/approve';
        
        /**
         * Commentable models
         * @var array commentableModels
         */
        public $commentableModels = array();
        
        /**
         * Action for posting comments, where add comment form is submited
         * @var postCommentAction
         */
        public $postCommentAction;
        
        /**
         * Settings for User model, used in application
         * @var userSettings
         */
        public $userConfig;
        
        /**
         * Default config for model
         * @var _defaultModelConfig
         */
        protected $_defaultModelConfig = array(
            //only registered users can post comments
            'registeredOnly' => false,
            'useCaptcha' => false,
            //allow comment tree
            'allowSubcommenting' => true,
            //display comments after moderation
            'premoderate' => false,
            //action for postig comment
            'postCommentAction' => 'comments/comment/postComment',
            //super user condition(display comment list in admin view and automoderate comments)
            'isSuperuser'=>'false',
            //order direction for comments
            'orderComments'=>'DESC',
            //settings for comments page url
            'pageUrl'=>null
        );
    
	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'comments.models.*',
			'comments.components.*',
		));
	}
        
        /*
         * Display ECommentsWidget for defined model and controller
         * @param CActiveRecord $model
         * @param CController $controller
         */
        public function outComments($model, $controller)
        {
            //if this model is commentable
            if(($modelConfig = $this->getModelConfig($model)) !== null)
            {
                $this->outCommentsList($model, $controller);
            }
        }
        
        /*
         * Returns settings for model. Model can be CActiveRecord instance or string. 
         * If there is no model settings, then return null
         * @param mixed $model 
         * @return mixed
         */
        public function getModelConfig($model)
        {
            $modelName = is_object($model) ? get_class($model) : $model; 
            $modelConfig = array();
            if(in_array($modelName, $this->commentableModels) || isset($this->commentableModels[$modelName]))
            {
                $modelConfig = isset($this->commentableModels[$modelName]) ? 
                    array_merge($this->_defaultModelConfig, $this->commentableModels[$modelName]) :
                    $this->_defaultModelConfig;
            }
            return $modelConfig;
        }
        
        /*
         * Sets default config for models
         * @param array $config
         */
        public function setDefaultModelConfig($config)
        {
            if(is_array($config))
                $this->_defaultModelConfig = array_merge($this->_defaultModelConfig, $config);
        }
}
