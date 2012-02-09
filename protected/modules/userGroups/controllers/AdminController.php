<?php
/**
 * @author Nicola Puddu
 * @package userGroups
 * admin controller
 */
class AdminController extends Controller
{
	/**
	 * @var mixed tooltip for the permission menagement
	 */
	public static $_permissionControl = array(
					'read'=>'view documentation.',
					'write'=>'can change permission of user and groups whose group level is below his own.',
					'admin'=>'can create users and groups and has all the permission of a user with user admin permissions. The level rule still apply.',
					);

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

			array('allow',  // allow all users to perform 'index' actions if they have the writing permission on admin
				'actions'=>array('index'),
				'pbac'=>array('write', 'admin'),
			),
			array('allow',  // allow all users to perform 'accessList' actions if they have the writing permission on admin
				'actions'=>array('accessList'),
				'ajax'=>true,
				'pbac'=>array('write', 'admin'),
			),
			array('allow',  // allow root to access the update action
				'actions'=>array('update'),
				'users'=>array(UserGroupsUser::ROOT),
			),
			array('allow',  // allow root to access the update action
				'actions'=>array('cron'),
				'ips'=>array('127.0.0.1'),
			),
			array('allow',  // allow all users to perform 'documentation' actions if they have the reading permission on admin
				'actions'=>array('documentation'),
				'pbac'=>array('read'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays the current configurations
	 */
	public function actionIndex()
	{
		// load the data provider and models for the grid views
		$confDataProvider = new CActiveDataProvider('UserGroupsConfiguration', array('pagination'=>array('pageSize' => 20),));
		$cronDataProvider = new CActiveDataProvider('UserGroupsCron', array('pagination'=>array('pageSize' => 10),));
		$groupModel = new UserGroupsGroup('search');
		$groupModel->unsetAttributes();
		$userModel = new UserGroupsUser('search');
		$userModel->unsetAttributes();

		// load the filtering data to the group model
		if(isset($_GET['UserGroupsGroup']))
			$groupModel->attributes=$_GET['UserGroupsGroup'];
		// load the filtering data to the user model
		if(isset($_GET['UserGroupsUser']))
			$userModel->attributes=$_GET['UserGroupsUser'];

		// checks if the configuration form has been sent
		if(isset($_POST['UserGroupsConfiguration']))
			$this->configurationSave($_POST['UserGroupsConfiguration']);

		// checks if the cron form has been sent
		if(isset($_POST['UserGroupsCron']))
			$this->cronSave($_POST['UserGroupsCron']);

		// checks if the cron remove form has been sent
		if (isset($_POST['UserGroupsCronRemove']))
			$this->cronRemove();

		// checks if the access form has been sent
		if(isset($_POST['UserGroupsAccess']) && !isset($_POST['UserGroupsAccess']['delete']))
			$this->accessPermissionSave($_POST['UserGroupsAccess']);

		if (isset($_POST['UserGroupsAccess']) && isset($_POST['UserGroupsAccess']['delete']))
			$this->itemDelete($_POST['UserGroupsAccess']);

		// checks if the page was loaded as ajax
		if (Yii::app()->request->isAjaxRequest)
			$this->renderPartial('index', array('confDataProvider'=>$confDataProvider, 'cronDataProvider'=>$cronDataProvider, 'groupModel' => $groupModel, 'userModel' => $userModel), false, true);
		else
			$this->render('index', array('confDataProvider'=>$confDataProvider, 'cronDataProvider'=>$cronDataProvider, 'groupModel' => $groupModel, 'userModel' => $userModel));
	}

	/**
	 * display the documentation
	 */
	public function actionDocumentation()
	{
		if (Yii::app()->request->isAjaxRequest)
			$this->renderPartial('documentation', NULL, false, true);
		else
			$this->render('documentation');
	}

	/**
	 * display the access list for a group or a user
	 */
	public function actionAccessList()
	{
		// check if the user is asking for root data
		if ((int)$_GET['id'] !== UserGroupsUser::ROOT) {

			// create the user/group model
			$model_name = (int)$_GET['what'] === UserGroupsAccess::USER ? 'UserGroupsUser' : 'UserGroupsGroup';
			$additionalData = new $model_name();

			// extract it's data if it's not a new record
			if ($_GET['id'] !== 'new')
				$additionalData = $additionalData->findByPk((int)$_GET['id']);

			if (Yii::app()->user->level > $additionalData->level) { // check if the user/group level is inferior to the user who is checking the permissions
				if ($_GET['id'] !== 'new' || ($_GET['id'] === 'new' && Yii::app()->user->pbac('admin'))) { // check if the user/group is new and if the user eventually has the permissions to create one
					// load the controllerlist of user or groups
					if ((int)$_GET['what'] === UserGroupsAccess::GROUP)
						$dataProvider = UserGroupsAccess::controllerList($_GET['id']);
					else
						$dataProvider = UserGroupsAccess::controllerList($additionalData->group_id, $_GET['id']);

					$this->renderPartial('accessList', array('dataProvider'=>$dataProvider, 'data'=>$additionalData, 'what'=>$_GET['what'], 'id'=>$_GET['id']));
				} else
					echo Yii::t('userGroupsModule.admin','You cannot create a new user or group');
			} else
				echo Yii::t('userGroupsModule.admin','You cannot change data or permissions of a user or a group with a level higher then yours');
		} else
			echo Yii::t('userGroupsModule.admin','You cannot change data or permissions of Root');
	}

	/**
	 * this action takes care of updating the db when necessary
	 */
	public function actionUpdate()
	{
		if (UserGroupsInstallation::VERSION > UserGroupsConfiguration::findRule('version')) {
			$this->render('/update/'.UserGroupsInstallation::VERSION);
		} else
			throw new CHttpException('403', 'userGroups is up to date');
	}

	/**
	 * this action executes every single cron
	 */
	public function actionCron()
	{
		if (UserGroupsConfiguration::findRule('server_executed_crons') !== true)
				return;

		UGCron::init();
		UGCron::add(new UGCJGarbageCollection);
		UGCron::add(new UGCJUnban);
		foreach (Yii::app()->controller->module->crons as $c) {
			UGCron::add(new $c);
		}
		UGCron::run();
	}

	/**
	 * save the access permission data for both user and groups
	 * @param Array $formData
	 */
	private function accessPermissionSave($formData)
	{
		if ((int)$formData['id'] === UserGroupsUser::ROOT)
			Yii::app()->user->setFlash($formData['what'],Yii::t('userGroupsModule.admin','You cannot change the Access Permissions of the Root User'));
		else if ($formData['id'] === 'new' && !Yii::app()->user->pbac('admin'))
			Yii::app()->user->setFlash($formData['what'],Yii::t('userGroupsModule.admin','You cannot create a new user or group'));
		else if ($formData['id'] !== 'new' && !is_numeric($formData['id']))
			Yii::app()->user->setFlash($formData['what'],Yii::t('userGroupsModule.admin','You didn\'t supply a valid id'));
		else {

			// if the user has the right permissions update all the other attributes load the model and load the attributes
			if (Yii::app()->user->pbac('admin.admin')) {
				// TODO when stop supporting php 5.2 just initialize the model with variables
				if ((int)$formData['what'] === UserGroupsAccess::GROUP) {
					// load or create the group
					if ($formData['id'] === 'new')
						$model=new UserGroupsGroup;
					else
						$model=UserGroupsGroup::model()->findByPk((int)$formData['id']);
				} else if ((int)$formData['what'] === UserGroupsAccess::USER) {
					// load or create the user
					if ($formData['id'] === 'new')
						$model=new UserGroupsUser;
					else
						$model=UserGroupsUser::model()->findByPk((int)$formData['id']);
				}
				$model->setScenario('admin');
				$model->attributes = $formData[$formData['what']];

				if ($model->validate()) {
					if (!$model->save()) {
						Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','A problem occurred during the Data and Access Permission save action'));
						$this->redirect(Yii::app()->baseUrl .'/userGroups/admin');
					}
				} else {
					Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','The following problems occurred: {errors}', array('{errors}'=>$this->errorParse($model->getErrors()) )));
					$this->redirect(Yii::app()->baseUrl .'/userGroups/admin');
				}
				$element_id = $model->id;
			}

			if (!isset($element_id)) { // fix for users with no admin permissions
				if ((int)$formData['what'] === UserGroupsAccess::GROUP)
					$element_id = UserGroupsGroup::model()->findByPk((int)$formData['id'])->id;
				else if ((int)$formData['what'] === UserGroupsAccess::USER)
					$element_id = UserGroupsUser::model()->findByPk((int)$formData['id'])->id;
			}

			if (isset($formData['access'])) {
				// initialize the array containing the existing permissions records
				$ex_array = array();
				// load the existing permissions for the element
				$existing = UserGroupsAccess::model()->findAllByAttributes(array('element'=> (int)$formData['what'], 'element_id' => $element_id));
				// iterate the existing permission
				if ($existing) {
					foreach ($existing as $e) {
						$ex_array[$e->id] = $e->module.'.'.$e->controller.'.'.$e->permission;
					}
				}


				// iterate the submitted permissions
				foreach ($formData['access'] as $key => $val) {
					// check if the permission already exist otherwise creates it
					if (array_search($key, $ex_array) === false) {
						// extract the permission data
						$k = explode('.', $key);
						// create the new permission
						$new_permission = new UserGroupsAccess;
						$new_permission->element = (int)$formData['what'];
						$new_permission->element_id = (int)$element_id;
						$new_permission->module = $k[0];
						$new_permission->controller = $k[1];
						$new_permission->permission = $k[2];
						$new_permission->save();

					} else {
						// find and delete the key from the ex_array
						unset($ex_array[array_search($key, $ex_array)]);
					}
				}


				// delete from the database the records corresponding to those still inside the ex_array
				if (count($ex_array))
					UserGroupsAccess::model()->deleteAll('id IN ('.implode(', ',array_flip($ex_array)).')');

			} else { // clear permissions
				UserGroupsAccess::model()->deleteAllByAttributes(array('element'=> (int)$formData['what'], 'element_id' => $element_id));
			}

			if ($formData['id'] === 'new')
				if ((int)$formData['what'] === UserGroupsAccess::USER)
					Yii::app()->user->setFlash('user', Yii::t('userGroupsModule.admin','New user Created.', array('{what}'=>$formData['what'])));
				else
					Yii::app()->user->setFlash('group', Yii::t('userGroupsModule.admin','New group Created.', array('{what}'=>$formData['what'])));
			else
				if ((int)$formData['what'] === UserGroupsAccess::USER)
					Yii::app()->user->setFlash('user', Yii::t('userGroupsModule.admin','Data and Access Permission of <i>{displayname} user</i> changed', array('{displayname}'=>$formData['displayname'])));
				else
					Yii::app()->user->setFlash('group', Yii::t('userGroupsModule.admin','Data and Access Permission of <i>{displayname} group</i> changed', array('{displayname}'=>$formData['displayname'])));
		}
		$this->redirect(Yii::app()->baseUrl .'/userGroups/admin');
	}

	/**
	 * deletes the item from the database
	 * @param Array $formData
	 */
	private function itemDelete($formData)
	{
		// check if the user performing the action has the permission to do it
		if (!Yii::app()->user->pbac('admin'))
			Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','You don\'t have the permission to delete any user/group'));
		else {
			// check if the user is trying to delete a valid id
			if(Yii::app()->request->isPostRequest && $formData['id'] !== 'new' && (int)$formData['id'] !== UserGroupsUser::ROOT) {
				// load the item to delete
				if ((int)$formData['what'] === UserGroupsAccess::GROUP)
					$model = UserGroupsGroup::model()->findByPk((int)$formData['id']);
				else if ((int)$formData['what'] === UserGroupsAccess::USER)
					$model = UserGroupsUser::model()->findByPk((int)$formData['id']);

				if ($model) {
					// check if your level is higher then the user/group you are about to delete
					if ($model->level < Yii::app()->user->level) {
						if ($model->delete() && UserGroupsAccess::model()->deleteAll('element = '.$formData['what']. ' AND element_id = '.$formData['id']))
							Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','{what} deleted.', array('{what}'=>ucfirst($formData['displayname']))));
						else
							Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','Impossible to delete the requested user/group. An Error Occurred'));

					} else
						Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','You cannot delete a user/group with a higher level then yours.'));
				} else
					Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','The requested user/group does not exist and cannot be deleted.'));
			} else
				Yii::app()->user->setFlash((int)$formData['what'] === UserGroupsAccess::USER ? 'user' : 'group', Yii::t('userGroupsModule.admin','Invalid Request.'));
		}
		$this->redirect(Yii::app()->baseUrl .'/userGroups/admin');
	}

	/**
	 * save the configurations data
	 * @param Array $formData
	 */
	private function configurationSave($formData)
	{
		if (Yii::app()->user->pbac('userGroups.admin.admin')) {
			$successes = 0;
			foreach($formData as $configuration => $value) {
				$model = $this->loadConfiguration($configuration);
				$model->value = $value;
				if ($model->save())
					$successes++;
			}
			if ($successes == count($formData))
				Yii::app()->user->setFlash('configuration', Yii::t('userGroupsModule.admin','New Configurations Saved'));
			else
				Yii::app()->user->setFlash('configuration', Yii::t('userGroupsModule.admin','A problem occurred during the configuration save action'));
		} else
			Yii::app()->user->setFlash('configuration', Yii::t('userGroupsModule.admin','You are not allowed to change any configuration'));

		$this->redirect(Yii::app()->baseUrl .'/userGroups/admin');
	}

	/**
	 * save the cron data
	 * @param Array $formData
	 */
	private function cronSave($formData)
	{
		if (Yii::app()->user->pbac('userGroups.admin.admin')) {
			$successes = 0;
			foreach($formData as $cron => $value) {
				$model = $this->loadCron($cron);
				$model->lapse = is_numeric($value) ? (int)$value : $model->lapse;
				if ($model->save())
					$successes++;
			}
			if ($successes == count($formData))
				Yii::app()->user->setFlash('crons', Yii::t('userGroupsModule.admin','Cron Jobs Configuration Saved'));
			else
				Yii::app()->user->setFlash('crons', Yii::t('userGroupsModule.admin','A problem occurred during the cron jobs configuration save action'));
		} else
			Yii::app()->user->setFlash('crons', Yii::t('userGroupsModule.admin','You are not allowed to change the cron jobs configuration'));

		$this->redirect(Yii::app()->baseUrl .'/userGroups/admin');
	}

	/**
	 * remove not installed cronjobs
	 */
	private function cronRemove()
	{
		// check user permissions
		if (Yii::app()->user->pbac('userGroups.admin.admin')) {
			// load the cronjobs
			UGCron::init();
			UGCron::add(new UGCJGarbageCollection);
			UGCron::add(new UGCJUnban);
			foreach (Yii::app()->controller->module->crons as $c) {
				UGCron::add(new $c);
			}
			// load the cronjobs
			$crons = UserGroupsCron::model()->findAll();
			foreach ($crons as $c) {
				if (UGCron::getStatus($c->name) === UGCron::NOT_INSTALLED)
					$c->delete();
			}
			Yii::app()->user->setFlash('crons', Yii::t('userGroupsModule.admin','Cron Jobs successfully removed'));
		} else
			Yii::app()->user->setFlash('crons', Yii::t('userGroupsModule.admin','You are not allowed to remove cron jobs'));

		$this->redirect(Yii::app()->baseUrl .'/userGroups/admin');
	}

	/**
	 * Returns the configuration based on the primary key given.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the configuration to be loaded
	 */
	public function loadConfiguration($id)
	{
		$model=UserGroupsConfiguration::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,Yii::t('userGroupsModule.admin','The requested rule does not exist.'));
		return $model;
	}

	/**
	 * Returns the configuration based on the primary key given.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the configuration to be loaded
	 */
	public function loadCron($id)
	{
		$model=UserGroupsCron::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,Yii::t('userGroupsModule.admin','The requested cron does not exist.'));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-groups-admin-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * returns a string containing all the errors of the validation
	 * @param Array $error_array
	 * @return String
	 */
	private function errorParse($error_array)
	{
		$error_string = NULL;
		foreach ($error_array as $p) {
			foreach ($p as $pe) {
				$error_string .= "<br/>$pe";
			}
		}
		return $error_string;
	}
}