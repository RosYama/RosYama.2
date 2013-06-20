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
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Страница обновления версии структуры БД
	 */
	public function actionDbupdate()
	{
		if(!Yii::app()->user->isAdmin)
		{
			$this->redirect('/');
			die();
		}
		define('UPDATES_DIRECTORY', $_SERVER['DOCUMENT_ROOT'].'/protected/data/dbupdates'); // <- notice the absence of trailing slash
		// get local installed and available versions
		$_version = Yii::app()->getGlobalState('db_version', array(0 => 0));
		$_versionAvailable = array(0 => 0);
		$dir = opendir(UPDATES_DIRECTORY);
		if($dir)
		{
			while($file = readdir($dir))
			{
				$file   = substr($file, 0, strpos($file, '.'));
				$letter = ltrim($file, '1234567890');
				if(!$letter)
				{
					$letter = 0;
				}
				$file = (int)preg_replace('/\D/', '', $file);
				$_versionAvailable[$letter] = max($_versionAvailable[$letter], $file);
			}
			closedir($dir);
		}
		if(isset($_GET["FORCE"]))
		{
			$letter = explode('_', $_GET['FORCE']);
			if(!isset($letter[1]))
			{
				$letter[1] = 0;
			}
			$_version[$letter[1]] = (int)$letter[0];
			Yii::app()->setGlobalState('db_version', $_version);
		}
		$output = '';
		if($_POST)
		{
			// the updating
			$_update_files = array();
			$dir = opendir(UPDATES_DIRECTORY);
			while($file = readdir($dir))
			{
				$fileid = substr($file, 0, strpos($file, '.'));
				$letter = ltrim($fileid, '1234567890');
				if(!$letter)
				{
					$letter = 0;
				}
				$fileid = (int)preg_replace('/\D/', '', $file);
				if($fileid > $_version[$letter])
				{
					$_update_files[$file.'|'.$fileid.'|'.$letter] = $file;
				}
			}
			closedir($dir);
			ksort($_update_files);
			$bOk = true;
			foreach($_update_files as $id_jajaja => $f_jajaja)
			{
				$result = require(UPDATES_DIRECTORY.'/'.$f_jajaja);
				if($result === false)
				{
					$bOk = false;
					$output .= 'Проклятье! Что-то пошло не так. Не удалось провести обновление файла '.$f_jajaja.'<br />';
					break;
				}
				$id_jajaja = explode('|', $id_jajaja);
				if(!$id_jajaja[2])
				{
					$id_jajaja[2] = 0;
				}
				$_version[$id_jajaja[2]] = (int)$id_jajaja[1];
				Yii::app()->setGlobalState('db_version', $_version);
				$output .= 'Скрипт обновлений '.$f_jajaja.' выполнен<br />';
			}
			if($bOk)
			{
				$output .= 'Все обновления выполнены<br><br>';
			}
		}
		$this->render
		(
			'dbupdate',
			array
			(
				'_version' => $_version,
				'_versionAvailable' => $_versionAvailable,
				'output' => $output
			)
		);
	}
}