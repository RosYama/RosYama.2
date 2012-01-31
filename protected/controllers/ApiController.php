<?php

class ApiController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout=false;

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
				'actions'=>array('add','update', 'personal','personalDelete','request','requestForm','sent','notsent','gibddreply', 'fix', 'defix', 'prosecutorsent', 'prosecutornotsent','delanswerfile','myarea', 'territorialGibdd'),
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
	
	public function actionIndex()
	{
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		$model->limit=Yii::app()->request->getQuery('limit');
		if (!$model->limit) $model->limit=30;
		$offset=Yii::app()->request->getQuery('offset');
		if (!$offset) $offset=0;
		$data=$model->search();
		
		$data->pagination->currentPage=(int)($offset/$model->limit);
		if (!$data->pagination->currentPage) $data->pagination->currentPage=1;
		
		$tags=Array();
		
		$tags[]=CHtml::tag('sort', array (), false, false);
		$tags[]=CHtml::closeTag('sort');
		$tags[]=CHtml::tag('navigation', array (), false, false);
			$tags[]=CHtml::tag('item', array ('code'=>'limit'), $model->limit, true);
			$tags[]=CHtml::tag('item', array ('code'=>'offset'), $offset, true);
		$tags[]=CHtml::closeTag('navigation');
		$tags[]=CHtml::tag('defectslist', array (), false, false);
			foreach ($data->data as $hole){
				$tags[]=CHtml::tag('hole', array ('id'=>$hole->ID), false, false);
					$tags[]=CHtml::tag('id', array (), $hole->ID, true);
					$tags[]=CHtml::tag('username', array ('full'=>$hole->user->Fullname), false, false);
						$tags[]=CHtml::tag('name', array (), $hole->user->name, true);
						$tags[]=CHtml::tag('secondname', array (), $hole->user->second_name, true);
						$tags[]=CHtml::tag('lastname', array (), $hole->user->last_name, true);
					$tags[]=CHtml::closeTag('username');
					$tags[]=CHtml::tag('latitude', array (), $hole->LATITUDE, true);
					$tags[]=CHtml::tag('longitude', array (), $hole->LONGITUDE, true);
					$tags[]=CHtml::tag('address', array ('city'=>$hole->ADR_CITY, 'subjectrf'=>$hole->ADR_SUBJECTRF), $hole->ADDRESS, true);
					$tags[]=CHtml::tag('state', array ('code'=>$hole->STATE), $hole->StateName, true);
					$tags[]=CHtml::tag('type', array ('code'=>$hole->type->alias), $hole->type->name, true);
					$tags[]=CHtml::tag('datecreated', array ('readable'=>date('d.m.Y',$hole->DATE_CREATED)), $hole->DATE_CREATED, true);
					$tags[]=CHtml::tag('datesent', array ('readable'=>$hole->DATE_SENT ? date('d.m.Y',$hole->DATE_SENT) : ''), $hole->DATE_SENT, true);
					$tags[]=CHtml::tag('datestatus', array ('readable'=>$hole->DATE_STATUS ? date('d.m.Y',$hole->DATE_STATUS) : ''), $hole->DATE_STATUS, true);
					$tags[]=CHtml::tag('commentfresh', array (), $hole->COMMENT1, true);
					$tags[]=CHtml::tag('commentfixed', array (), $hole->COMMENT2, true);
					$tags[]=CHtml::tag('commentgibddre', array (), false, true);
				$tags[]=CHtml::closeTag('hole');	
				}
		$tags[]=CHtml::closeTag('defectslist');
		$this->renderXml($tags);
	}
	
	public function renderXml($tags)
	{		
		$this->render('xml',array(
		'tags'=>$tags,
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
