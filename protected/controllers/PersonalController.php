<?php

class PersonalController extends Controller
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
			'accessControl', // perform access control for CRUD operations
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
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
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
        
        
		$this->render('view',array(
			'hole'=>$this->loadModel($id),

		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Holes;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Holes']))
		{
			$model->attributes=$_POST['Holes'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->ID));
		}

		$this->render('create',array(
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
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Holes']))
		{
			$model->attributes=$_POST['Holes'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->ID));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->layout='//layouts/header_default';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Holes']))
			$model->attributes=$_POST['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
			
		$this->render('index',array(
			'model'=>$model,
		));
	}
	
	public function actionHoles()
	{
		$this->layout='//layouts/header_default';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Holes']))
			$model->attributes=$_POST['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
			
		$this->render('index',array(
			'model'=>$model,
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
