<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public $layout='//layouts/header_default'; 
	
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}
	
	public function actionOpenID()
	{
				$loid = Yii::app()->loid->load();
		if (!empty($_GET['openid_mode'])) {
			if ($_GET['openid_mode'] == 'cancel') {
				$err = Yii::t('core', 'Authorization cancelled');
			} else {
				try {
					echo $loid->validate() ? 'Logged in.' : 'Failed';
				} catch (Exception $e) {
					$err = Yii::t('core', $e->getMessage());
				}
			}
			if(!empty($err)) echo $err;
		} else {
			$loid->identity = "http://my.openid.identifier"; //Setting identifier
			$loid->required = array('namePerson/friendly', 'contact/email'); //Try to get info from openid provider
			$loid->realm     = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; 
			$loid->returnUrl = $loid->realm . $_SERVER['REQUEST_URI']; //getting return URL
			if (empty($err)) {
				try {
					$url = $loid->authUrl();
					$this->redirect($url);
				} catch (Exception $e) {
					$err = Yii::t('core', $e->getMessage());
				}
			}
		}
	}	

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		if(isset($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], 'forum.rosyama') || strpos($_SERVER['HTTP_REFERER'], 'forum.dev.rosyama')))
		{
			Yii::app()->homeUrl = $_SERVER['HTTP_REFERER'];
		}
		$this->redirect(Yii::app()->homeUrl);
	}
}