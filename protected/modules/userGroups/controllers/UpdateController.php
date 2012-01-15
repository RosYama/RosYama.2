<?php

class UpdateController extends Controller
{
	/**
	 * @var mixed no permission rules for this controller
	 */
	public static $_permissionControl = false;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'userGroupsAccessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow root to access the update action
				'actions'=>array('execute'),
				'users'=>array(UserGroupsUser::ROOT),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays the module home page content according to the user status
	 */
	public function actionExecute()
	{
		$update = 'update'.str_replace('.', '_', $_GET['v']);
		$this->$update();
	}

	private function update1_8() {
		if (UserGroupsConfiguration::findRule('version') >= 1.8)
				return;

		mkdir(Yii::app()->basePath.'/views/ugmail');
		// add the activation mail view
		$path = Yii::app()->basePath . '/views/ugmail/activation.php';
		$content = file_get_contents(Yii::app()->basePath . '/modules/userGroups/templates/template_activation.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}
		// add the invitation mail view
		$path = Yii::app()->basePath . '/views/ugmail/invitation.php';
		$content = file_get_contents(Yii::app()->basePath . '/modules/userGroups/templates/template_invitation.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}
		// add the password reset mail view
		$path = Yii::app()->basePath . '/views/ugmail/passreset.php';
		$content = file_get_contents(Yii::app()->basePath . '/modules/userGroups/templates/template_passreset.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}
		// add new cron configuration
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'server_executed_crons';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'if true crons must be executed from the server using a crontab';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));

		// change version number
		$version_number = UserGroupsConfiguration::model()->findByAttributes(array('rule' => 'version'));
		$version_number->scenario = 'installation';
		$version_number->value = '1.8';
		$version_number->save();


		return true;
	}
}