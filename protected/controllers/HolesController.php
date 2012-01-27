<?php

class HolesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'findSubject', 'findCity', 'map', 'ajaxMap'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('add','update', 'personal','personalDelete','request','sent','notsent','gibddreply', 'fix', 'defix', 'prosecutorsent', 'prosecutornotsent','delanswerfile','myarea'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'moderate'),
				'groups'=>array('root', 'admin', 'moder'), 
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionFindSubject()
	{
	
		$q = $_GET['term'];
       if (isset($q)) {
           $criteria = new CDbCriteria;           
           $criteria->params = array(':q' => trim($q).'%');
           $criteria->condition = 'name LIKE (:q)'; 
           $RfSubjects = RfSubjects::model()->findAll($criteria); 
 
           if (!empty($RfSubjects)) {
               $out = array();
               foreach ($RfSubjects as $p) {
                   $out[] = array(
                       // expression to give the string for the autoComplete drop-down
                       //'label' => preg_replace('/('.$q.')/i', "<strong>$1</strong>", $p->name_full),  
                       'label' =>  $p->name_full,  
                       'value' => $p->name_full,
                       'id' => $p->id, // return value from autocomplete
                   );
               }
               echo CJSON::encode($out);
               Yii::app()->end();
           }
       }
	}
	
	public function actionFindCity()
		{
		
			$q = $_GET['Holes']['ADR_CITY'];
		   if (isset($q)) {
			   $criteria = new CDbCriteria;           
			   $criteria->params = array(':q' => trim($q).'%');
			   if (isset($_GET['Holes']['ADR_SUBJECTRF']) && $_GET['Holes']['ADR_SUBJECTRF']) $criteria->condition = 'ADR_CITY LIKE (:q) AND ADR_SUBJECTRF='.$_GET['Holes']['ADR_SUBJECTRF']; 
			   else $criteria->condition = 'ADR_CITY LIKE (:q)'; 
			   $criteria->group='ADR_CITY';
			   $Holes = Holes::model()->findAll($criteria); 
	 
			   if (!empty($Holes)) {
				   $out = array();
				   foreach ($Holes as $p) {
					   $out[] = array(
						   // expression to give the string for the autoComplete drop-down
						   //'label' => preg_replace('/('.$q.')/i', "<strong>$1</strong>", $p->name_full),  
						   'label' =>  $p->ADR_CITY,    
						   'value' => $p->ADR_CITY,
					   );
				   }
				   echo CJSON::encode($out);
				   Yii::app()->end();
			   }
		   }
		}	

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/hole_view.css'); 
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
        $jsFile = CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'view_script.js');
        $cs->registerScriptFile($jsFile);
        
		$this->render('view',array(
			'hole'=>$this->loadModel($id),

		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAdd()
	{
		$model=new Holes;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');

		if(isset($_POST['Holes']))
		{
			$model->attributes=$_POST['Holes'];
			$model->USER_ID=Yii::app()->user->id;	
			$model->DATE_CREATED=time();
			$subj=RfSubjects::model()->SearchID(trim($model->STR_SUBJECTRF));
			if($subj) $model->ADR_SUBJECTRF=$subj;
			else $model->ADR_SUBJECTRF=0;
			$model->ADR_CITY=trim($model->ADR_CITY);
			
			if (Yii::app()->user->level > 50) $model->PREMODERATED=1;
			else $model->PREMODERATED=0;
			
			if($model->save() && $model->savePictures())
				$this->redirect(array('view','id'=>$model->ID));
		}
		else {
		//выставляем центр на карте по координатам IP юзера
		$request=new CHttpRequest;
		$geoIp = new EGeoIP();
		$geoIp->locate($request->userHostAddress); 	
		//echo ($request->userHostAddress);
		if ($geoIp->longitude) $model->LATITUDE=$geoIp->longitude;
		if ($geoIp->latitude) $model->LONGITUDE=$geoIp->latitude;
		}

		$this->render('add',array(
			'model'=>$model,			
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$this->layout='//layouts/header_user';
		
		$model=$this->loadChangeModel($id);
		
		if($model->STATE!='fresh')	
			throw new CHttpException(403,'Доступ запрещен.');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');

		if(isset($_POST['Holes']))
		{
			$model->attributes=$_POST['Holes'];
			if ($model->STR_SUBJECTRF){
				$subj=RfSubjects::model()->SearchID(trim($model->STR_SUBJECTRF));
				if($subj) $model->ADR_SUBJECTRF=$subj;
			}
			if($model->save() && $model->savePictures())
				$this->redirect(array('view','id'=>$model->ID));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	public function actionGibddreply($id)
	{
		$this->layout='//layouts/header_user';
		
		$model=$this->loadModel($id);
		$model->scenario='gibdd_reply';
		if($model->STATE!='inprogress' && $model->STATE!='achtung' && !$model->request_gibdd)	
			throw new CHttpException(403,'Доступ запрещен.');

		$answer=new HoleAnswers;
		if (isset($_GET['answer']) && $_GET['answer'])
			$answer=HoleAnswers::model()->findByPk((int)$_GET['answer']);
			
		$answer->request_id=$model->request_gibdd->id;
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);

		if(isset($_POST['HoleAnswers']))
		{
			$answer->attributes=$_POST['HoleAnswers'];
			//if (isset($_POST['HoleAnswers']['results'])) $answer->results=$_POST['HoleAnswers']['results'];
			$answer->date=time();
			if($answer->save()){
				if ($model->STATE=="inprogress")
					$model->STATE='gibddre';
				$model->GIBDD_REPLY_RECEIVED=1;
				if (!$model->DATE_STATUS) $model->DATE_STATUS=time();
				if ($model->update())
					$this->redirect(array('view','id'=>$model->ID));
				}
		}

		$this->render('gibddreply',array(
			'model'=>$model,
			'answer'=>$answer,
		));
	}
	
	public function actionFix($id)
	{
		$this->layout='//layouts/header_user';
		
		$model=$this->loadModel($id);
		if (!$model->request_gibdd || !$model->request_gibdd->answers)
			throw new CHttpException(403,'Доступ запрещен.');			
			
		$model->scenario='fix';
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);

		if(isset($_POST['Holes']))
		{
			$model->STATE='fixed';
			$model->COMMENT2=$_POST['Holes']['COMMENT2'];
			$model->DATE_STATUS=time();
				if ($model->save() && $model->savePictures())
					$this->redirect(array('view','id'=>$model->ID));
		}

		$this->render('fix_form',array(
			'model'=>$model,	
			'newimage'=>new PictureFiles
		));
	}	
	
	public function actionDefix($id)
	{
		$model=$this->loadChangeModel($id);
		$model->updateSetinprogress();
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}	

	//удаление ямы админом или модером
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest && (isset($_POST['id']) || $_POST['DELETE_ALL']))
		{
			if (!isset($_POST['DELETE_ALL'])){
			$id=$_POST['id'];
			// we only allow deletion via POST request
			$model=$this->loadModel($id);
			if (isset($_POST['banuser']) && $_POST['banuser']){
				$reason="Забанен";
				$period=100000;
					$usermodel = UserGroupsUser::model()->findByPk($model->USER_ID); 
					$usermodel->setScenario('ban');
					// check if you are trying to ban a user with an higher level
					if ($usermodel->relUserGroupsGroup->level >= Yii::app()->user->level)
						Yii::app()->user->setFlash('user', 'Вы не можете банить пользователей с уровнем выше или равным вашему.');
					else {
						$usermodel->ban = date('Y-m-d H:i:s', time() + ($period * 86400));
						$usermodel->ban_reason = $reason;
						$usermodel->status = UserGroupsUser::BANNED;
						if ($usermodel->update())
							Yii::app()->user->setFlash('user', '{$usermodel->username}\ акаунт забанен до {$usermodel->ban}.');
						else
							Yii::app()->user->setFlash('user', 'Произошла ошибка попробуйте немного позднее');
					}
				}
				
				$model->delete();
			}
			else {
				$holes=Holes::model()->findAll('id IN ('.$_POST['DELETE_ALL'].')');
				$ok=0;
				foreach ($holes as $model)
					if ($model->delete()) $ok++;
				if ($ok==count($holes))  echo 'ok';
			}			

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	//удаление ямы пользователем
	public function actionPersonalDelete($id)
	{

			$model=$this->loadChangeModel($id);				
			$model->delete();			
			
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('personal'));
		
	}	

	//генерация запросов в ГИБДД
	public function actionRequest($id)
	{
			$model=$this->loadModel($id);				
			$request=new HoleRequestForm;
			if(isset($_POST['HoleRequestForm']))
			{
				$request->attributes=$_POST['HoleRequestForm'];
				$_images = array();
				$date3 = $request->application_data   ? strtotime($request->application_data) : time();
				if ($request->form_type == 'prosecutor')
					$date3 = strtotime($request->application_data);
					
				$date2 = $request->form_type == 'prosecutor' && $model->request_gibdd ? $model->request_gibdd->date_sent  : time();
				$_data = array
				(
					'chief'       => $request->to,
					'fio'         => $request->from,
					'address'     => $request->postaddress,
					'date1.day'   => date('d', $model->DATE_CREATED),
					'date1.month' => date('m', $model->DATE_CREATED),
					'date1.year'  => date('Y', $model->DATE_CREATED),
					'street'      => $request->address,
					'date2.day'   => date('d', $date2),
					'date2.month' => date('m', $date2),
					'date2.year'  => date('Y', $date2),
					'signature'   => $request->signature,
					'reason'      => $request->comment,
					'date3.day'   => date('d', $date3),
					'date3.month' => date('m', $date3),
					'date3.year'  => date('Y', $date3),
					'gibdd'       => $request->gibdd,
					'gibdd_reply' => $request->gibdd_reply
				);
			
				if($request->html)
				{
					foreach($model->pictures['original']['fresh'] as $src)
					{
						$_images[] = $src;
					}
					header('Content-Type: text/html; charset=utf8', true);
					$HT = new html1234();
					$HT->gethtml
					(
						$request->form_type ? $request->form_type : $model->type,
						$_data,
						$_images
					);
				}
				else
				{
					header_remove('Content-Type');	
					foreach($model->pictures['original']['fresh'] as $src)
					{
						$_images[] = $_SERVER['DOCUMENT_ROOT'].$src;
					}
					header('Content-Type: application/pdf; charset=utf-8', true);
					$PDF = new pdf1234();
					$PDF->getpdf
					(
						$request->form_type ? $request->form_type : $model->type,
						$_data,
						$_images
					);
				}
			}		
	}		

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->layout='//layouts/header_default';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		//$model->PREMODERATED;
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
		$dataProvider=$model->search();	
		$this->render('index',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}
	
	public function actionModerate($id)
	{
		if (!isset($_GET['PREMODERATE_ALL'])){
			$model=$this->loadModel($id);
			if (!$model->PREMODERATED) {
				$model->PREMODERATED=1;
				if ($model->update()) echo 'ok';
				}
		}
		else {
			$holes=Holes::model()->findAll('id IN ('.$_GET['PREMODERATE_ALL'].')');
			$ok=0;
			foreach ($holes as $model)
			if (!$model->PREMODERATED) {
				$model->PREMODERATED=1;
				if ($model->update()) $ok++;
				}
			if ($ok==count($holes))  echo 'ok';
		}
	}
	
	public function actionSent($id)
	{
		$model=$this->loadModel($id);
		$model->makeRequest('gibdd');
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}
	
	public function actionProsecutorsent($id)
	{
		$model=$this->loadModel($id);
		$model->makeRequest('prosecutor');
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}
	
	public function actionProsecutornotsent($id)
	{
		$model=$this->loadModel($id);
		$model->updateRevokep();
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}		
	
	public function actionNotsent($id)
	{
		$model=$this->loadModel($id);
		$model->updateRevoke();
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}	
	
	//удаление файла ответа гибдд
	public function actionDelanswerfile($id)
	{
			$file=HoleAnswerFiles::model()->findByPk((int)$id);
			
			if (!$file)
				throw new CHttpException(404,'The requested page does not exist.');
				
			if ($file->answer->request->user_id!=Yii::app()->user->id && !Yii::app()->user->isModer && $file->answer->request->hole->STATE !='gibddre')
				throw new CHttpException(403,'Доступ запрещен.');
				
			$file->delete();			
			
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$file->answer->request->hole->ID));
		
	}	
	
	public function actionPersonal()
	{
		$this->layout='//layouts/header_user';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		$user=Yii::app()->user;
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/holes_list.css');
		
		$holes=Array();
		$all_holes_count=0;
		foreach ($model->AllstatesMany as $state_alias=>$state_name) {
			$holes[$state_alias]=Holes::model()->findAll(Array('condition'=>'USER_ID='.$user->id.' AND STATE="'.$state_alias.'"', 'order'=>'DATE_CREATED DESC'));		
			$all_holes_count+=count($holes[$state_alias]);
		}
			
		$this->render('personal',array(
			'model'=>$model,
			'holes'=>$holes,
			'all_holes_count'=>$all_holes_count,
			'user'=>$user
		));
	}
	
	public function actionMyarea()
	{
		$user=Yii::app()->user;
		$area=$user->userModel->hole_area;
		if (!$area)	$this->redirect(array('/profile/myarea'));
		
		$this->layout='//layouts/header_user';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/holes_list.css');
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
        $jsFile = CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'area_script.js');
        $cs->registerScriptFile($jsFile);        
		
		$holes=Array();
		$all_holes_count=0;		
		foreach ($model->AllstatesMany as $state_alias=>$state_name) {
			$criteria=new CDbCriteria;
			foreach ($area as $shape){
			$criteria->addCondition('LATITUDE >= '.$shape->points[0]->lat
			.' AND LATITUDE <= '.$shape->points[2]->lat
			.' AND LONGITUDE >= '.$shape->points[0]->lng
			.' AND LONGITUDE <= '.$shape->points[2]->lng, 'OR');
			}
			$criteria->addCondition('STATE="'.$state_alias.'"');
			$criteria->order='DATE_CREATED DESC';
			$holes[$state_alias]=Holes::model()->findAll($criteria);		
			$all_holes_count+=count($holes[$state_alias]);
		}
			
		$this->render('myarea',array(
			'model'=>$model,
			'holes'=>$holes,
			'all_holes_count'=>$all_holes_count,
			'user'=>$user,
			'area'=>$area
		));
	}		
	
	public function actionMap()
	{
		//$this->layout='//layouts/header_default';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Holes']))
			$model->attributes=$_POST['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
			
		$this->render('map',array(
			'model'=>$model,
			'types'=>HoleTypes::model()->findAll(Array('condition'=>'t.published=1', 'order'=>'ordering')),
		));
	}
	
	public function actionAjaxMap()
	{
		$criteria=new CDbCriteria;
		/// Фильтрация по масштабу позиции карты
		if (isset ($_GET['bottom'])) $criteria->addCondition('LATITUDE > '.(float)$_GET['bottom']);
		if (isset ($_GET['left'])) $criteria->addCondition('LONGITUDE > '.(float)$_GET['left']);		
		if (isset ($_GET['right'])) $criteria->addCondition('LONGITUDE < '.(float)$_GET['right']);		
		if (isset ($_GET['top'])) $criteria->addCondition('LATITUDE < '.(float)$_GET['top']);		
		if (isset ($_GET['exclude_id']) && $_GET['exclude_id']) $criteria->addCondition('ID != '.(int)$_GET['exclude_id']); 
		if (!Yii::app()->user->isModer) $criteria->compare('PREMODERATED',1);
	
		/// Фильтрация по состоянию ямы
		if(isset($_GET['Holes']['STATE']) && $_GET['Holes']['STATE'])
		{
			$criteria->addInCondition('STATE', $_GET['Holes']['STATE']);
		}
		
		/// Фильтрация по типу ямы
		if(isset($_GET['Holes']['type']) && $_GET['Holes']['type'])
		{
			$criteria->addInCondition('TYPE_ID', $_GET['Holes']['type']);
		}
		
		// определение, нужна ли премодерация
		/*$raw = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/index.php');
		preg_match('/(\'|\")PREMODERATION\1 => (\"|\')(Y|N|)\2/', $raw, $_match);
		if($_match[3] == 'Y')
		{
			$arFilter['PREMODERATED'] = 1;
		}*/
		
		/// Если не заданы параметры, то производится выборка всех записей ям
		/*if(!$_GET['bottom'] && !$_GET['top'] && !$_GET['bottom'] && !$_GET['right'])
		{
			$res = C1234Hole::GetList();
		}
		else
		{
			$res = C1234Hole::GetList
			(
				array(),
				$arFilter,
				array('nopicts' => true)
			);
		}*/
		
		$res = Holes::model()->findAll($criteria);	
		
		$markers=Array();
		
		foreach($res as &$hole)
		{
			if(!isset($_REQUEST['skip_id']) || $_REQUEST['skip_id'] != $hole['ID'])
			{
				$markers[]=Array('id'=>$hole->ID, 'type'=>$hole->type->alias, 'lat'=>$hole->LONGITUDE, 'lng'=>$hole->LATITUDE, 'state'=>$hole->STATE);				
			}
		}
		echo $_GET['jsoncallback'].'({"markers": '.CJSON::encode($markers).'})';
		Yii::app()->end();		
		
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Holes']))
			$model->attributes=$_GET['Holes'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Holes::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	//Лоадинг модели для пользовательских изменений
	public function loadChangeModel($id)
	{
		$model=Holes::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		elseif(!$model->IsUserHole)	
			throw new CHttpException(403,'Доступ запрещен.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='holes-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
