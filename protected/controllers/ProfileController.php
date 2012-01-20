<?php

class ProfileController extends Controller
{
	#static $_permissionControl = array('label' => 'Better Label');

	/**
	 * @return array action filters
	 */
	public $layout='//layouts/header_user';
	
	public function filters()
	{
		return array(
			'userGroupsAccessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',  // just guest can perform 'activate' and 'login' actions
				#'ajax'=>false,
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */

	public function actionIndex()
	{
		$id=Yii::app()->user->id;
		$miscModel=$this->loadModel($id, 'changeMisc');
		$passModel= clone $miscModel;
		$passModel->setScenario('changePassword');
		$passModel->password = NULL;

		// pass the models inside the array for ajax validation
		$ajax_validation = array($miscModel, $passModel);

		// load additional profile models
		$profile_models = array();
		$profiles = Array('Profile');
		foreach ($profiles as $p) {
			$external_profile = new $p;
			// check if the loaded profile has an update view
			$external_profile_views = $external_profile->profileViews();
			if (array_key_exists(UserGroupsUser::EDIT, $external_profile_views)) {
				// load the model data
				$loaded_data = $external_profile->findByAttributes(array('ug_id' => $id));
				$external_profile = $loaded_data ? $loaded_data : $external_profile;
				// set the scenario
				$external_profile->setScenario('updateProfile');
				// load the models inside both the ajax validation array and the profile models
				// array to pass it to the view
				$profile_models[$p] = $external_profile;
				$ajax_validation[] = $external_profile;
			}
		}

		// perform ajax validation
		$this->performAjaxValidation($ajax_validation);
		//print_r($profile_models);
		// check if an additional profile model form was sent
		if ($form = array_intersect_key($_POST, array_flip($profiles))) {
			$model_name = key($form);
			$form_values = reset($form);
			// load the form values into the model
			$miscModel->relProfile->attributes = $form_values;
			$miscModel->relProfile->ug_id = $id;			
			// save the model
			if ($miscModel->relProfile->save()) {	
				Yii::app()->user->setFlash('user', Yii::t('userGroupsModule.general','Data Updated Successfully'));
				//$this->redirect(Yii::app()->baseUrl . '/userGroups?_isAjax=1&u='.$passModel->username);
			} else {
				//Yii::app()->user->setFlash('user', Yii::t('userGroupsModule.general','An Error Occurred. Please try later.'));
				}
		}
		

		if(isset($_POST['UserGroupsUser']) && isset($_POST['formID']))
		{
			// pass the right model according to the sended form and load the permitted values
			if ($_POST['formID'] === 'user-groups-password-form')
				$model = $passModel;
			else if ($_POST['formID'] === 'user-groups-misc-form')
				$model = $miscModel;

			$model->attributes = $_POST['UserGroupsUser'];

			if ($model->validate()) {
				if ($model->save()) {
					Yii::app()->user->setFlash('user', Yii::t('userGroupsModule.general','Data Updated Successfully'));
					//$this->renderPartial('update',array('miscModel'=>$miscModel,'passModel'=>$passModel, 'profiles' => $profile_models), false, true);
					//$this->redirect(Array('/holes/personal'));
					//$this->redirect(Yii::app()->baseUrl . '/userGroups?_isAjax=1&u='.$model->username);
				} else
					Yii::app()->user->setFlash('user', Yii::t('userGroupsModule.general','An Error Occurred. Please try later.'));
			}
		}

		$this->render('update',array('miscModel'=>$miscModel,'passModel'=>$passModel, 'profiles' => $profile_models), false, true);
	}
	 
	 
	public function actionLoad()
	{
		$model= Profile::model()->findByAttributes(array('ug_id' => Yii::app()->user->id));
        if(isset($_POST['Profile']))
        {
			$this->performAjaxValidation($model);

            $model->attributes=$_POST['Profile'];
            $model->avatar=CUploadedFile::getInstance($model,'avatar');
            if($model->save())
            {
               $model->avatar->saveAs('avatars/'.Yii::app()->user->name.'.jpg');


            } else
				Yii::app()->user->setFlash('user', 'you can just upload images.');

			$this->redirect(array('/userGroups'));
        }
	}
	
	public function loadModel($id, $scenario = false)
	{
		$model=UserGroupsUser::model()->findByPk((int)$id);
		if($model===null || ($model->relUserGroupsGroup->level > Yii::app()->user->level && !UserGroupsConfiguration::findRule('public_profiles')))
			throw new CHttpException(404,Yii::t('userGroupsModule.general','The requested page does not exist.'));
		if ($scenario)
			$model->setScenario($scenario);
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']))
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}