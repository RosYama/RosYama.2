<?php
/**
 * This Module is for Users and Groups management
 * For more info check the documentation inside the module.
 * You can access documentation just when the module is installed.
 * if you want to check the documentation before installing the module
 * go to the module page on Yii website
 * @author Nicola Puddu
 * @package userGroups
 */
class UserGroupsModule extends CWebModule
{
	/**
	 * access code for authentication during installation
	 * @var string
	 */
	public $accessCode;
	/**
	 * additional salt to use for cripting the user password
	 * @var string
	 */
	public $salt;
	/**
	 * additional cronjobs to run
	 * every value in this array must be the class name of an
	 * implementation of UGCronJob
	 * @var array
	 */
	public $crons = array();
	/**
	 * additional profile models
	 * every value in this array must be a CActiveRecord
	 * class name.
	 * @var array
	 */
	public $profile = array();
	/**
	 * specify the classes containing the mail messages
	 * @var array
	 */
	public $mailMessages = array();
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		
		$this->defaultController = 'UGDefault';

		// import the module-level models and components
		$this->setImport(array(
			'userGroups.models.*',
			'userGroups.components.*',
		));
		// register the css and js files
		Yii::app()->clientScript->registerCoreScript('jquery');
		// load css and js if the page wasn't loaded with ajax
		if (!Yii::app()->request->isAjaxRequest) {
			Yii::app()->clientScript->registerCssFile(Yii::app()->assetManager->publish(Yii::getPathOfAlias('userGroups') . '/css').'/userGroups.css');
			Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->publish(Yii::getPathOfAlias('userGroups') . '/js/userGroups.js'));
		}
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// check if the module is installed and make the right redirections
			if (!in_array(Yii::app()->db->tablePrefix.'usergroups_configuration',  Yii::app()->db->getSchema()->getTableNames()) && strpos(Yii::app()->request->pathInfo, 'userGroups/install') === false)
				Yii::app()->request->redirect(Yii::app()->baseUrl . '/userGroups/install');
			if (in_array(Yii::app()->db->tablePrefix.'usergroups_configuration',  Yii::app()->db->getSchema()->getTableNames()) && strpos(Yii::app()->request->pathInfo, 'userGroups/install') !== false)
				Yii::app()->request->redirect(Yii::app()->baseUrl . '/userGroups/');
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
