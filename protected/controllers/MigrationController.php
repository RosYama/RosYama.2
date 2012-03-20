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
			array('allow',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function sql_valid($data) { 
	  $data = str_replace("\\", "\\\\", $data); 
	  $data = str_replace("'", "\'", $data); 
	  $data = str_replace('"', '\"', $data); 
	  $data = str_replace("\x00", "\\x00", $data); 
	  $data = str_replace("\x1a", "\\x1a", $data); 
	  $data = str_replace("\r", "\\r", $data); 
	  $data = str_replace("\n", "\\n", $data); 
	  return($data);  
	 } 
	
	public function actionIndex()
	{
	$this->layout='//layouts/blank';
	$this->render('index',array(
		));		
	}	
	
	public function actionDelthis()
	{
	$base=$_SERVER['DOCUMENT_ROOT'];
	unlink($base.'/protected/views/migration/index.php');
	rmdir($base.'/protected/views/migration');
	unlink($base.'/protected/models/BHoles.php');
	unlink($base.'/protected/models/BUser.php');
	unlink($base.'/protected/models/BUserGroup.php');
	unlink($base.'/protected/controllers/MigrationController.php');
	$this->redirect(array('holes/index'));
	}
	
	
	public function actionMakedata()
	{
		if (DLDatabaseHelper::import($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'protected'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'rosyama_blank.sql')) echo 'Структура данных успешно создана';
		else echo 'Произошла ошибка'; 
	}
	
	public function actionImportUsers()
	{
		set_time_limit(0);
		$count=0;
		$users=BUser::model()->findAll();
		foreach ($users as $user){
			$group=BUserGroup::model()->find('USER_ID='.$user->ID);
			if ($group && $group->GROUP_ID!=0){	
				$username='';
				$user->LOGIN=$this->sql_valid($user->LOGIN);
				$model=UserGroupsUser::model()->find("username='".$user->LOGIN."'");
				if ($model) {
					if ($user->EXTERNAL_AUTH_ID && $user->XML_ID){
						$username=$user->LOGIN.'_'.$user->EXTERNAL_AUTH_ID;
						$username=substr($username, 0,110);						
						$model=new UserGroupsUser('import');
						$model->username=$username;
						}
					elseif($model->external_auth_id && $model->xml_id ) {
						$model->username=$model->username.'_'.$model->xml_id;
						$model->update();
						$model=new UserGroupsUser('import');
					}	
				}
				if (!$model) $model=new UserGroupsUser('import');
				$group_id=2;
				if ($group->GROUP_ID==1) $group_id=5;
				if ($group->GROUP_ID==4) $group_id=3;
				if ($user->LOGIN=='admin') $group_id=1;
				
				if ($user->EXTERNAL_AUTH_ID && $user->XML_ID) $user->PASSWORD='';
				
				$model->attributes=Array(
					'id'=>$user->ID,
					'group_id'=>$group_id,
					'username'=>$username ? $username : $user->LOGIN,
					'password'=>$user->PASSWORD,
					'email'=>trim($user->EMAIL) ? trim($user->EMAIL) : null,
					'name'=>$user->NAME,
					'second_name'=>$user->SECOND_NAME,
					'last_name'=>$user->LAST_NAME,
					'home'=>'',
					'status'=>4,
					'creation_date'=>$user->DATE_REGISTER,
					'activation_code'=>null,
					'activation_time'=>$user->CHECKWORD_TIME,
					'last_login'=>$user->LAST_LOGIN,
					'params'=>array_keys($model->ParamsFields),
					'xml_id'=>$user->XML_ID,
					'external_auth_id'=>$user->EXTERNAL_AUTH_ID,
					'is_bitrix_pass'=>1,
				);
				$model->id=$user->ID;
				if ($model->save()){
					$count++;
					if (!$model->relProfile){
					$profile=new Profile;
					$profile->ug_id=$model->id;
					$profile->birthday=$user->PERSONAL_BIRTHDAY;
					$profile->site=$user->PERSONAL_WWW;
					$profile->save();
					}
					$model->creation_date=$user->DATE_REGISTER;
					$model->status=4;
					$model->update();
				}
				else{
					echo "Пользователь ".$user->ID."(".$user->LOGIN.")"." не добавлен из-за : \n";
					print_r($model->errors);
					}
			}
		}
		echo "Добавлено $count пользователей.";
		
	}

	
	public function actionImportHoles()
	{
		set_time_limit(0);
		$holes=BHoles::model()->findAll();
		$count=0;
		foreach ($holes as $hole){
		//if (1){
		if ($hole->picturenames && isset($hole->picturenames['medium']['fresh']) && count($hole->picturenames['medium']['fresh']) > 0){
			$model=new Holes('import');
			$model->attributes=$hole->attributes;
			$model->ID=$hole->ID;
			$type=HoleTypes::model()->find('alias = "'.$hole->TYPE.'"');
			$model->TYPE_ID=$type->id;
			if ($model->ADR_SUBJECTRF){
				$gibdd=GibddHeads::model()->find('is_regional=1 AND subject_id='.$model->ADR_SUBJECTRF);
				if ($gibdd) $model->gibdd_id=$gibdd->id;
				else $model->gibdd_id=0;
			}
			else $model->gibdd_id=0;
			  
			//if ($model->errors) print_r ($model->errors);			
			if ($model->STATE=="inprogress") {
			if (!$model->DATE_SENT) $model->DATE_SENT=$model->DATE_CREATED;
			//echo date('d.m.Y', $model->DATE_SENT).'<br/>';
			}
			if ($model->save()){
			//if (0){
				$count++;
				foreach($hole->picturenames['medium']['fresh'] as $i=>$src){
					$picture=new HolePictures;
					$picture->type='fresh'; 
					$picture->filename=$src;
					$picture->hole_id=$hole->ID;
					$picture->user_id=$model->USER_ID;
					$picture->ordering=$i;
					$picture->save();
				}
				foreach($hole->picturenames['medium']['fixed'] as $i=>$src){
					$picture=new HolePictures;
					$picture->type='fixed'; 
					$picture->filename=$src;
					$picture->hole_id=$hole->ID;
					$picture->user_id=$model->USER_ID;
					$picture->ordering=$i;
					$picture->save();
				}
				if ($model->STATE=="fixed"){
						$fixmodel=new HoleFixeds;
						$fixmodel->user_id=$model->USER_ID;
						$fixmodel->hole_id=$model->ID;
						$fixmodel->date_fix=$model->DATE_STATUS;
						$fixmodel->comment=$model->COMMENT2;
						$fixmodel->save();
				}
				
				if ($model->STATE!="fresh"){
				if (!$model->DATE_SENT) $model->DATE_SENT=$model->DATE_CREATED;
				$request=new HoleRequests;
				$request->hole_id=$model->ID;
				$request->user_id=$model->USER_ID;
				$request->gibdd_id=$model->gibdd_id;
				$request->date_sent=$model->DATE_SENT;
				$request->type='gibdd';
				$request->save();
					if ($model->GIBDD_REPLY_RECEIVED){ 
						$answer=new HoleAnswers('import');
						//$answer->attributes=Array('uppload_files'=>Array(123,456));
						$answer->isimport=true;
						$answer->request_id=$request->id;
						$answer->date=$model->DATE_STATUS;
						$answer->comment=$model->COMMENT_GIBDD_REPLY;						
						if ($answer->save()){
							$dir=$_SERVER['DOCUMENT_ROOT'].$answer->filesFolder;
							if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/'))
								mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/');
							if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/'.$answer->request->hole->ID))
								mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/'.$answer->request->hole->ID);
							if (!is_dir($dir))
								mkdir($dir);
							if (!is_dir($dir.'/thumbs'))
								mkdir($dir.'/thumbs');
							foreach($hole->picturenames['medium']['gibddreply'] as $i=>$src){
								$pict=new HoleAnswerFiles;
								$pict->file_name=$src;
								$pict->file_type='image';
								$pict->answer_id=$answer->id;
								copy($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$answer->request->hole->ID.'/'.$src, $dir.'/'.$src);
									//unlink ($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$src);
								copy($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$answer->request->hole->ID.'/'.$src, $dir.'/thumbs/'.$src);
									//unlink ($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$src);
								$pict->save();	
							}
						}
						else {
							print_r($answer->errors);	
							die();
							}
						
					}	
				}
			}
			
			
		}
	}
	echo "Добавлено $count ям.";
	}
	
}
