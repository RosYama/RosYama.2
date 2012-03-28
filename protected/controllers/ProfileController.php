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

	public function actionUpdate()
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
		if (!$miscModel->relProfile) $miscModel->relProfile=new Profile;
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
				Yii::app()->user->setFlash('user', 'Данные успешно обновлены');
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
			
			unset ($_POST['UserGroupsUser']['group_id'], $_POST['UserGroupsUser']['creation_date']);
			
			$model->attributes = $_POST['UserGroupsUser'];			
			
			//$model->unsetAttributes(Array('group_id','creation_date'));
			if ($model->validate()) {
			if ($model->username!=$miscModel->username){
				$model->xml_id=''; $model->external_auth_id='';
			}
				if ($model->save()) {
					Yii::app()->user->setFlash('user', 'Данные успешно обновлены');
					$this->refresh();
					//$this->renderPartial('update',array('miscModel'=>$miscModel,'passModel'=>$passModel, 'profiles' => $profile_models), false, true);
					//$this->redirect(Array('/holes/personal'));
					//$this->redirect(Yii::app()->baseUrl . '/userGroups?_isAjax=1&u='.$model->username);
				} else
					Yii::app()->user->setFlash('user', 'Произошла ошибка. Попробуйте позже.');
			}
		}		
		$this->render('update',array('miscModel'=>$miscModel,'passModel'=>$passModel, 'profiles' => $profile_models), false, true);
	}
	
	public function actionMyarea()
	{
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
        $model=$this->loadModel(Yii::app()->user->id);	
        
        	if(isset($_POST['UserAreaShapes']))
			{
				$ids=Array();
				foreach ($_POST['UserAreaShapes'] as $i=>$shape)
					{
						if ($shape['id']) $ids[]=$shape['id'];	
					}
				if ($ids){
				$delshapes=UserAreaShapes::model()->findAll('id NOT IN ('.implode(',',$ids).') AND ug_id='.$model->id);				
				foreach ($delshapes as $shape) $shape->delete();
				}
				
				foreach ($_POST['UserAreaShapes'] as $i=>$shape)
					{
						$shapemodel=$shape['id'] ? UserAreaShapes::model()->findByPk((int)$shape['id']) : new UserAreaShapes;
						if ($shapemodel){
							$shapemodel->ug_id=$model->id;
							$shapemodel->ordering=$shape['ordering'];
							if ($shapemodel->isNewRecord) $shapemodel->save();
							$ii=0;
							foreach ($_POST['UserAreaShapePoints'][$i] as $point)
							{
								if ($ii>=$shapemodel->countPoints) break;
								$pointmodel=$point['id'] ? UserAreaShapePoints::model()->findByPk((int)$point['id']) : new UserAreaShapePoints;				
								$pointmodel->attributes=$point;
								$pointmodel->shape_id=$shapemodel->id;
								$pointmodel->save();
								$ii++;
							}
							if (!$shapemodel->points) $shapemodel->delete();
						}
					}	
				$this->redirect(array('/holes/myarea'));
			}        
      	$this->render('myarea',array('model'=>$model));
	}
	
	public function actionMyareaAddshape()
	{		
      	if (isset($_POST['i'])) $this->renderPartial('_area_point_fields',array('shape'=>new UserAreaShapes, 'i'=>$_POST['i'], 'form'=>new CActiveForm));
	}
	
	//сохрание списка ям в избраное
	public function actionSaveHoles2Selected($id, $holes)
	{
		if ($id){
			$gibdd=GibddHeads::model()->findByPk((int)$id);
			$holemodel=Holes::model()->findAllByPk(explode(',',$holes));
			if ($gibdd && $holemodel) {
				$model=new UserSelectedLists;
				$model->user_id=Yii::app()->user->id;
				$model->gibdd_id=$gibdd->id;
				$model->date_created=time();
				$model->holes=$holemodel;
				$model->save();					
			}
		}
		$p = Yii::app()->createController('holes');
		$p[0]->actionSelectHoles(false);		
	}	
	
	//удаление списка ям
	public function actionDelHolesSelectList($id)
	{
		$model=UserSelectedLists::model()->findByPk((int)$id);	
		if ($model && $model->user_id==Yii::app()->user->id) $model->delete();
		$p = Yii::app()->createController('holes');
		$p[0]->actionSelectHoles(false);
	}		

	
	
	public function actionView($id)
	{
		$this->layout='main';
		$model= $this->loadModel($id);
		$contactModel=new ContactForm;
		if(isset($_POST['ContactForm']) && $model->getParam('showContactForm'))
		{
			$contactModel->attributes=$_POST['ContactForm'];
			if($contactModel->validate())
			{
				$headers="From: ".Yii::app()->params['adminEmail']."\r\nReply-To: ".Yii::app()->user->email;
				$headers = "MIME-Version: 1.0\r\nFrom: ".Yii::app()->params['adminEmail']."\r\nReply-To: ".Yii::app()->user->email."\r\nContent-Type: text/html; charset=utf-8";
				Yii::app()->request->baseUrl=Yii::app()->request->hostInfo;
				$mailbody=$this->renderPartial('application.views.ugmail.user2user', Array(
						'fromuser'=> Yii::app()->user->userModel,
						'touser'=>$model,
						'model'=>$contactModel,
						),true);
				mail($model->email,'РосЯма: личное сообщение - '.$contactModel->subject,$mailbody,$headers);
				Yii::app()->user->setFlash('contact','Сообщение успешно отправлено');
				$this->refresh();
			}
		}
		$this->render('view',array('model'=>$model,'contactModel'=>$contactModel));
	}		 
	 
	
	public function loadModel($id, $scenario = false)
	{
		$model=UserGroupsUser::model()->findByPk((int)$id);
		//if($model===null || ($model->relUserGroupsGroup->level > Yii::app()->user->level && !UserGroupsConfiguration::findRule('public_profiles')))
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
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