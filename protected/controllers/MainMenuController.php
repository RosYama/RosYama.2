<?php

class MainMenuController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/header_user';

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
			array('allow',
				'groups'=>array('root', 'admin'), 
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Menu;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Menu']))
		{
			$model->attributes=$_POST['Menu'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
       	
		if(isset($_POST['MainMenu']))
		{
			$model->attributes=$_POST['MainMenu'];
			if (!$model->controller) $model->action='';
			if (!isset($_POST['MainMenu']['element'])) $model->element='';
			if($model->save())
				$this->redirect(array('index'));
			else echo "Ошибка при сохранении";
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

 	public function actionDynamicActions()
	{

		 $data=MainMenu::model()->getContActions($_POST['controller']);

		    $data=CHtml::listData($data,'name','name');
		    //echo CHtml::tag('option', array('value'=>'0'),CHtml::encode('Выберете представление...'),true);
		    foreach($data as $value=>$name)
		    {
		        echo CHtml::tag('option',
		                   array('value'=>$value),CHtml::encode($name),true);
		    }


	}

	public function actionDynamicElements()
	{

			if ($_POST['MainMenu']['action']){
					$model=new MainMenu;
					$action=$_POST['MainMenu']['action'];
					$controller=$_POST['controller'];


					echo $model->getActionElements($controller, $action);


            }
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->deleteNode(true);

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
	public function actionUp($id){
	$node = MainMenu::model()->findByPK($id);
	$node->moveLeft();
	}

	public function actionDown($id){
	$node = MainMenu::model()->findByPK($id);
	$node->moveRight();
	}

	public function actionIndex()
	{
		$model = MainMenu::model()->findByPK(1);
       	if (isset($_POST['tree'])){
		        if($_POST['tree'] == 'manage') {

		            $node = MainMenu::model()->findByPK($_POST['node']);
		            $nodeTo = MainMenu::model()->findByPK($_POST['nodeto']);

		            // Добавление узла
		            if(isset($_POST['add'])) {
		                $newNode = new MainMenu();
		                $newNode->name = $_POST['name'];
		                $newNode->link = '';
		                $node->appendChild($newNode);
		            }

		            // Удаление узла
		            if(isset($_POST['delete'])) {
		                $node->deleteNode(true);
		            }

		            // Перемещение узла на уровень выше
		            if(isset($_POST['up'])) {
		                $node->moveLeft();
		            }

		            // Перемещение узла на уровень ниже
		            if(isset($_POST['down'])) {
		                $node->moveRight();
		            }

		            // Перемещение узла А перед узлом Б
		            if(isset($_POST['before'])) {
		              if($nodeTo->id>1) $node->moveBefore($nodeTo);
		            }

		            // Переместить узел А внутрь узла Б (в подкатегорию)
		            if(isset($_POST['below'])) {
		                $node->moveBelow($nodeTo);
		            }

		            $this->refresh();
		        }
        }

        // создаем массив, который будем кормить CDropdownList и CListBox
        $data = CHtml::listData($model->findAll(array('order'=>'lft')), 'id', 'nameExtWithRoot'); // Здесь переопределяем поле name на nameExt. Ниже описано зачем.
        /*$var = soap::request("GetCountries",array(
        'POST' =>'http://online.tourtrans.ru/tourinfo/FullTourInfoService.asmx HTTP/1.1',
		'Host' => 'online.tourtrans.ru',
		'Content-Type'=>'text/xml; charset=utf-8',
		'Content-Length'=>'length',
		'SOAPAction'=>'GetCountries',
        ));
		echo "!!!".$var."--!";    */

		if (isset($_GET['pageSize'])) {
		Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
		unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
		}

        $this->render('index',array(
            'data'=>$data,
            'model'=>MainMenu::model(),
            'tree'=>$model->findAll(array('order'=>'lft')),
        ));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new MainMenu('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MainMenu']))
			$model->attributes=$_GET['MainMenu'];

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
		$model=MainMenu::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='menu-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}