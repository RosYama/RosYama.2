<?php

/**
 * Класс, предоставляющий возможность совместной авторизации с форумом.
 * @see http://dev.rosyama.ru/bugs/show_bug.cgi?id=223
 * @author Дмитрий Никифоров (Dmitry Nikiforov) <axshavan@yandex.ru>
 */
class ForumService
{
	public $isAuthenticated;
	public $serviceName;
	public $id;
	public $external_auth_id;

	public function init()
	{
		$this->isAuthenticated = true;
		$this->serviceName = $this->getServiceName();
		$this->id = false;
		$this->external_auth_id = false;
	}

	/**
	 * Получить id службы авторизации совместной с форумом
	 * @return string
	 */
	public function getServiceName()
	{
		return isset(Yii::app()->components['eauth']->services['forum']['id'])
			? Yii::app()->components['eauth']->services['forum']['id']
			: 'forum';
	}

	/**
	 * Получить title службы авторизации совместной с форумом
	 * @return string
	 */
	public function getServiceTitle()
	{
		return isset(Yii::app()->components['eauth']->services['forum']['title'])
			? Yii::app()->components['eauth']->services['forum']['title']
			: 'Форум';
	}

	public function getAttribute($string)
	{
		return false;
	}

	public function getServiceType() {}
	public function getJsArguments() {}

	public function authenticate()
	{
		$session =& Yii::app()->session;
		if(isset($_GET['finished']))
		{
			if(!isset($session['forum_secretkey']) || !isset($_GET['secretkey']))
			{
				return false;
			}
			$secretkey = $session['forum_secretkey'];
			if($secretkey != $_GET['secretkey'])
			{
				return false;
			}
			$this->id = $session['forum_uid'];
			$session['forum_noredirect'] = true;
			return true;
		}
		elseif(!isset($_GET['uid']))
		{
			$secretkey = md5(time().mt_rand(0, 10000));
			$session['forum_secretkey'] = $secretkey;
			echo '<script type="text/javascript">document.location="http://forum.'.$_SERVER['HTTP_HOST'].'/rosyama.php?secretkey='.$secretkey.'&username='.htmlspecialchars(Yii::app()->user->name).'";</script>';
		}
		elseif(isset($_GET['uid']))
		{
			if(!isset($session['forum_secretkey']) || !isset($_GET['secretkey']))
			{
				return false;
			}
			$secretkey = $session['forum_secretkey'];
			if($secretkey != $_GET['secretkey'])
			{
				return false;
			}
			$session['forum_uid'] = (int)$_GET['uid'];
			echo '<script type="text/javascript">document.location="http://forum.'.$_SERVER['HTTP_HOST'].'/rosyama.php?secretkey='.$secretkey.'&rosyamauserid='.(int)(Yii::app()->user->id).'";</script>';
		}
		die();
	}
}

?>