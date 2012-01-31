<?php

class MigrationController extends Controller
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
				'actions'=>array('convertPictures'),
				'groups'=>array('root', 'admin'), 
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	
	public function actionConvertPictures()
	{
		$model=Holes::model()->findAll();
		
		

		foreach ($model as $hole){
			foreach($hole->picturenames['medium']['fresh'] as $i=>$src){
				$picture=new HolePictures;
				$picture->type='fresh'; 
				$picture->filename=$src;
				$picture->hole_id=$hole->ID;
				$picture->ordering=$i;
				$picture->save();
			}
			foreach($hole->picturenames['medium']['fixed'] as $i=>$src){
				$picture=new HolePictures;
				$picture->type='fixed'; 
				$picture->filename=$src;
				$picture->hole_id=$hole->ID;
				$picture->ordering=$i;
				$picture->save();
			}
			//еще надо на ответы ГИБДД сделать
			foreach($hole->picturenames['medium']['gibddreply'] as $i=>$src){
				
			}
		}
	}
	
}
