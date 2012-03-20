<?php

class XmlController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout=false;

	/**
	 * @return array action filters
	 */	
	
	
	public function getErrors(){
		return Array(
			'NOT_FOUND'=>Array(CHtml::tag('error', array ('code'=>"NOT_FOUND"), 'Запрашиваемый ресурс не найден', true)),
			'AUTHORIZATION_REQUIRED'=>Array(CHtml::tag('error', array ('code'=>"AUTHORIZATION_REQUIRED"), 'Требуется авторизация', true)),
			'CANNOT_REALISE_SUBJECTRF'=>Array(CHtml::tag('error', array ('code'=>"CANNOT_REALISE_SUBJECTRF"), 'Невозможно определить субъект РФ', true)),
			'CANNOT_REALISE_CITY'=>Array(CHtml::tag('error', array ('code'=>"CANNOT_REALISE_CITY"), 'Невозможно определить город', true)),
			'NOT_IMPLEMENTED'=>Array(CHtml::tag('error', array ('code'=>"NOT_IMPLEMENTED"), 'Метод не реализован', true)),
			'NO_FILES'=>Array(CHtml::tag('error', array ('code'=>"NO_FILES"), 'Не загружено ни одного файла', true)),
			'TOO_BIG_FILE'=>Array(CHtml::tag('error', array ('code'=>"TOO_BIG_FILE"), 'Слишком большой файл', true)),
			'TOO_MANY_FILES'=>Array(CHtml::tag('error', array ('code'=>"TOO_MANY_FILES"), 'Слишком много файлов', true)),
			'PARTIALLY_UPLOADED_FILE'=>Array(CHtml::tag('error', array ('code'=>"PARTIALLY_UPLOADED_FILE"), 'Файл загружен только частично', true)),
			'CANNOT_UPLOAD_FILE'=>Array(CHtml::tag('error', array ('code'=>"CANNOT_UPLOAD_FILE"), 'Невозможно загрузить файл', true)),
			'UNKNOWN_MIME_TYPE'=>Array(CHtml::tag('error', array ('code'=>"UNKNOWN_MIME_TYPE"), 'Неподдерживаемый тип файла', true)),
			'UNKNOWN_IMAGE_FORMAT'=>Array(CHtml::tag('error', array ('code'=>"UNKNOWN_IMAGE_FORMAT"), 'Неподдерживаемый формат изображения', true)),
			'INCORRECT_TYPE'=>Array(CHtml::tag('error', array ('code'=>"INCORRECT_TYPE"), 'Неправильный тип дефекта', true)),
			'DEPRECATED_TYPE'=>Array(CHtml::tag('error', array ('code'=>"DEPRECATED_TYPE"), 'Неиспользуемый в данный момент тип дефекта', true)),
			'CANNOT_ADD_DEFECT'=>Array(CHtml::tag('error', array ('code'=>"CANNOT_ADD_DEFECT"), 'Невозможно добавить дефект', true)),
			'LATITUDE_NOT_SET'=>Array(CHtml::tag('error', array ('code'=>"LATITUDE_NOT_SET"), 'Не указана широта дефекта', true)),
			'LONGITUDE_NOT_SET'=>Array(CHtml::tag('error', array ('code'=>"LONGITUDE_NOT_SET"), 'Не указана долгота дефекта', true)),
			'NO_ADDRESS'=>Array(CHtml::tag('error', array ('code'=>"NO_ADDRESS"), 'Не указан адрес', true)),
			'WRONG_CREDENTIALS'=>Array(CHtml::tag('error', array ('code'=>"WRONG_CREDENTIALS"), 'Неправильный логин и/или пароль', true)),
			'UNAPPROPRIATE_METHOD'=>Array(CHtml::tag('error', array ('code'=>"UNAPPROPRIATE_METHOD"), 'Неподходящий метод', true)),
			'CANNOT_UPDATE_DEFECT'=>Array(CHtml::tag('error', array ('code'=>"CANNOT_UPDATE_DEFECT"), 'Не удалось обновить дефект', true)),
			'CANNOT_DELETE_DEFECT'=>Array(CHtml::tag('error', array ('code'=>"CANNOT_DELETE_DEFECT"), 'Не удалось удалить дефект', true)),
			'GEOCODE_ERROR'=>Array(CHtml::tag('error', array ('code'=>"GEOCODE_ERROR"), 'Не удалось произвести геокодирование', true)),
			'GEOCODE_EMPTY_REQUEST'=>Array(CHtml::tag('error', array ('code'=>"GEOCODE_EMPTY_REQUEST"), 'Пустой запрос к геокодеру', true)),
			'INTERNAL'=>Array(CHtml::tag('error', array ('code'=>"INTERNAL"), 'Иная внутренняя ошибка', true)),
		);
	}
	
	public function getUploadError($str){
		$tags=Array(CHtml::tag('error', array ('code'=>"UPLOAD_ERROR"), CHtml::encode($str), true));
		$this->renderXml($tags);
		Yii::app()->end();
	}	
	
	public function actionIndex($id=null, $user=null)
	{
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		$model->limit=Yii::app()->request->getParam('limit');
		$model->PREMODERATED=1;
		if ($user){
			$model->unsetAttributes(Array('PREMODERATED'));	
			$model->USER_ID=$user->id;
			}
		if ($id) $model->ID=(int)$id;
		if (Yii::app()->request->getParam('filter_rf_subject_id')) $model->ADR_SUBJECTRF=(int)Yii::app()->request->getParam('filter_rf_subject_id');
		if (Yii::app()->request->getParam('filter_city')) $model->ADR_CITY=Yii::app()->request->getParam('filter_city');
		if (Yii::app()->request->getParam('filter_status')) $model->STATE=Yii::app()->request->getParam('filter_status');
		if (Yii::app()->request->getParam('filter_type')) $model->type_alias=Yii::app()->request->getParam('filter_type');
		$page=Yii::app()->request->getParam('page');
		if (!$model->limit) $model->limit=30;
		$offset=Yii::app()->request->getParam('offset');
		if (!$offset) $offset=0;
		$data=$model->search();
		
		if (!$page)
			$data->pagination->currentPage=(int)($offset/$model->limit);
		else $data->pagination->currentPage=$page;
		$tags=Array();
		if (!$model->ID){
		$tags[]=CHtml::tag('sort', array (), false, false);
			$tags[]=CHtml::tag('item', array ('code'=>$data->sort->orderBy), CHtml::encode($data->sort->descTag), true);
		$tags[]=CHtml::closeTag('sort');
		$tags[]=CHtml::tag('filter', array (), false, false);
			$tags[]=CHtml::tag('item', array ('code'=>'PREMODERATED'), CHtml::encode($model->PREMODERATED), true);
			$tags[]=CHtml::tag('item', array ('code'=>'filter_rf_subject_id'), CHtml::encode($model->ADR_SUBJECTRF), true);
			$tags[]=CHtml::tag('item', array ('code'=>'filter_city'), CHtml::encode($model->ADR_CITY), true);
			$tags[]=CHtml::tag('item', array ('code'=>'filter_status'), CHtml::encode($model->STATE), true);
			$tags[]=CHtml::tag('item', array ('code'=>'filter_type'), CHtml::encode($model->type_alias), true);			
		$tags[]=CHtml::closeTag('filter');
		$tags[]=CHtml::tag('navigation', array (), false, false);
			$tags[]=CHtml::tag('item', array ('code'=>'limit'), CHtml::encode($model->limit), true);
			$tags[]=CHtml::tag('item', array ('code'=>'offset'), CHtml::encode($offset/$model->limit), true);
		$tags[]=CHtml::closeTag('navigation');
		}
		$tags[]=CHtml::tag('defectslist', array (), false, false);
			foreach ($data->data as $hole){
				$tags[]=CHtml::tag('hole', array ('id'=>$hole->ID), false, false);
					$tags[]=CHtml::tag('id', array (), CHtml::encode($hole->ID), true);
					$tags[]=CHtml::tag('username', array ('full'=>$hole->user->Fullname), false, false);
						$tags[]=CHtml::tag('name', array (), CHtml::encode($hole->user->name), true);
						$tags[]=CHtml::tag('secondname', array (), CHtml::encode($hole->user->second_name), true);
						$tags[]=CHtml::tag('lastname', array (), CHtml::encode($hole->user->last_name), true);
					$tags[]=CHtml::closeTag('username');
					$tags[]=CHtml::tag('latitude', array (), CHtml::encode($hole->LATITUDE), true);
					$tags[]=CHtml::tag('longitude', array (), CHtml::encode($hole->LONGITUDE), true);
					$tags[]=CHtml::tag('address', array ('city'=>$hole->ADR_CITY, 'subjectrf'=>$hole->ADR_SUBJECTRF), CHtml::encode(($hole->subject ? $hole->subject->name_full.', ' : '') .$hole->ADR_CITY.', '.$hole->ADDRESS), true);
					$tags[]=CHtml::tag('state', array ('code'=>$hole->STATE), CHtml::encode($hole->StateName), true);
					$tags[]=CHtml::tag('type', array ('code'=>$hole->type->alias), CHtml::encode($hole->type->name), true);
					$tags[]=CHtml::tag('datecreated', array ('readable'=>date('d.m.Y',$hole->DATE_CREATED)), CHtml::encode($hole->DATE_CREATED), true);
					$tags[]=CHtml::tag('datesent', array ('readable'=>$hole->DATE_SENT ? date('d.m.Y',$hole->DATE_SENT) : ''), CHtml::encode($hole->DATE_SENT), true);
					$tags[]=CHtml::tag('datestatus', array ('readable'=>$hole->DATE_STATUS ? date('d.m.Y',$hole->DATE_STATUS) : ''), CHtml::encode($hole->DATE_STATUS), true);
					$tags[]=CHtml::tag('commentfresh', array (), CHtml::encode($hole->COMMENT1), true);
					$tags[]=CHtml::tag('commentfixed', array (), CHtml::encode($hole->COMMENT2), true);
					$tags[]=CHtml::tag('commentgibddre', array (), false, true);
					$tags[]=CHtml::tag('pictures', array (), false, false);
						$tags[]=CHtml::tag('original', array (), false, false);
							$tags[]=CHtml::tag('fresh', array (), false, false);
								foreach ($hole->pictures_fresh as $pict){
								$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($pict->original), true);
								}
							$tags[]=CHtml::closeTag('fresh');
							$tags[]=CHtml::tag('fixed', array (), false, false);
								foreach ($hole->pictures_fixed as $pict){
								$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($pict->original), true);
								}								
							$tags[]=CHtml::closeTag('fixed');
							$tags[]=CHtml::tag('gibddreply', array (), false, false);				
							$tags[]=CHtml::closeTag('gibddreply');
						$tags[]=CHtml::closeTag('original');
						
						$tags[]=CHtml::tag('medium', array (), false, false);
							$tags[]=CHtml::tag('fresh', array (), false, false);
								foreach ($hole->pictures_fresh as $pict){
								$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($pict->medium), true);
								}
							$tags[]=CHtml::closeTag('fresh');
							$tags[]=CHtml::tag('fixed', array (), false, false);
								foreach ($hole->pictures_fixed as $pict){
								$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($pict->medium), true);
								}								
							$tags[]=CHtml::closeTag('fixed');
							$tags[]=CHtml::tag('gibddreply', array (), false, false);								
								foreach ($hole->requests_gibdd as $request)
									foreach ($request->answers as $answer)
										foreach ($answer->files_img as $pict){
										$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($answer->filesFolder.'/'.$pict->file_name), true);
										}
							$tags[]=CHtml::closeTag('gibddreply');
						$tags[]=CHtml::closeTag('medium');
						
						$tags[]=CHtml::tag('small', array (), false, false);
							$tags[]=CHtml::tag('fresh', array (), false, false);
								foreach ($hole->pictures_fresh as $pict){
								$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($pict->small), true);
								}
							$tags[]=CHtml::closeTag('fresh');
							$tags[]=CHtml::tag('fixed', array (), false, false);
								foreach ($hole->pictures_fixed as $pict){
								$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($pict->small), true);
								}								
							$tags[]=CHtml::closeTag('fixed');
							$tags[]=CHtml::tag('gibddreply', array (), false, false);								
								foreach ($hole->requests_gibdd as $request)
									foreach ($request->answers as $answer)
										foreach ($answer->files_img as $pict){
										$tags[]=CHtml::tag('src', array ('id'=>$pict->id), CHtml::encode($answer->filesFolder.'/thumbs/'.$pict->file_name), true);
										}
							$tags[]=CHtml::closeTag('gibddreply');
						$tags[]=CHtml::closeTag('small');
					
					$tags[]=CHtml::closeTag('pictures');
					$tags[]=CHtml::tag('gibddrequests', array (), false, false);	
						foreach ($hole->requests_gibdd as $request){
						$tags[]=CHtml::tag('request', array ('id'=>$request->id, 'gibdd_id'=>$request->gibdd_id,'date'=>$request->date_sent,'user_id'=>$request->user_id, 'user_name'=>$request->user->Fullname
						), false, false);
								foreach ($request->answers as $answer){
								$tags[]=CHtml::tag('answer', array ('id'=>$answer->id, 'date'=>$answer->date), false, false);
									$tags[]=CHtml::tag('files', array (), false, false);
										foreach ($answer->files as $pict){
										$tags[]=CHtml::tag('file', array ('id'=>$pict->id, 'type'=>$pict->file_type), CHtml::encode($answer->filesFolder.'/'.$pict->file_name), true);
										}
									$tags[]=CHtml::closeTag('files');	
								$tags[]=CHtml::closeTag('answer');	
								}		
						$tags[]=CHtml::closeTag('request');	
						}
					$tags[]=CHtml::closeTag('gibddrequests');					
				$tags[]=CHtml::closeTag('hole');	
				}
		$tags[]=CHtml::closeTag('defectslist');
		if (!$data->data && $id) $this->error('NOT_FOUND');	
		$this->renderXml($tags);
	}
	
	public function actionGetregions()
	{
		$model=RfSubjects::model()->findAll(Array('order'=>'id'));
		$tags=Array();
		$tags[]=CHtml::tag('regionslist', array (), false, false);
		foreach ($model as $item){
			$tags[]=CHtml::tag('region', array ('id'=>$item->id), CHtml::encode($item->name_full), true);
		}
		$tags[]=CHtml::closeTag('regionslist');
		$this->renderXml($tags);
	}
	
	public function actionAuthorize()
	{
		$tags=Array();
		$user=$this->auth();
		$tags[]=CHtml::tag('user', array ('id'=>$user->id), false, false);
			$tags[]=CHtml::tag('username', array ('full'=>$user->Fullname), false, false);
				$tags[]=CHtml::tag('name', array (), CHtml::encode($user->userModel->name), true);
				$tags[]=CHtml::tag('secondname', array (), CHtml::encode($user->userModel->second_name), true);
				$tags[]=CHtml::tag('lastname', array (), CHtml::encode($user->userModel->last_name), true);	
			$tags[]=CHtml::closeTag('username'); 		
			$tags[]=CHtml::tag('passwordhash', array (), CHtml::encode($user->userModel->password), true);
		$tags[]=CHtml::closeTag('user'); 
		$this->renderXml($tags);
		
	}
	
	public function actionCheckauth()
	{
		$tags=Array();
		if (Yii::app()->user->isGuest)
			$tags[]=CHtml::tag('checkauthresult', array ('result'=>0), 'fail', true);	
		else
			$tags[]=CHtml::tag('checkauthresult', array ('result'=>1), 'ok', true);
		$this->renderXml($tags);		
	}
	
	public function actionExit()
	{
		$tags=Array();
		Yii::app()->user->logout();
		$tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);		
		$this->renderXml($tags);		
	}
	
	public function actionMy($id=null)
	{
		$user=$this->auth();
		$this->actionIndex($id,$user);
	}	
	
	public function actionGetfileuploadlimits()
	{
		$tags=Array();
		$tags[]=CHtml::tag('maxpostsize', array (), ini_get('post_max_size'), true);		
		$tags[]=CHtml::tag('maxfilesize', array (), ini_get('upload_max_filesize'), true);
		$tags[]=CHtml::tag('maxfilescount', array (), ini_get('max_file_uploads'), true);
		$this->renderXml($tags);
	}
	
	public function actionAdd($id=null)
	{
		$user=$this->auth();
		$address=Yii::app()->request->getParam('address');
		if (!$address) $this->error('NO_ADDRESS'); 
		$latitude=Yii::app()->request->getParam('latitude');
		if (!$latitude) $this->error('LATITUDE_NOT_SET'); 
		$longitude=Yii::app()->request->getParam('longitude');
		if (!$longitude) $this->error('LONGITUDE_NOT_SET'); 
		$comment=Yii::app()->request->getParam('comment');
		$gibdd_id=Yii::app()->request->getParam('gibdd_id');
		$type=Yii::app()->request->getParam('type');
		if (!$type) $this->error('INCORRECT_TYPE');
		else {
			$typemodel=HoleTypes::model()->find('alias="'.$type.'"');
			if (!$typemodel) $this->error('INCORRECT_TYPE');
			elseif (!$typemodel->published) $this->error('DEPRECATED_TYPE'); 
			}
		
		$addressArr    = RfSubjects::model()->Address($address);
		$subject_rf = $addressArr['subject_rf'];
		$city       = $addressArr['city'];
		$address    = $addressArr['address'];
		
		if((!$subject_rf || !$city || !$address) && ($latitude && $longitude)){
				$addressArr    = RfSubjects::model()->AddressfromLatLng($latitude, $longitude, $this->mapkey);
					if ($addressArr) {
						$subject_rf = $addressArr['subject_rf'];
						$city       = $addressArr['city'];
						$address    = $addressArr['address'];	
					}
			}
		
		// ворнинги, если надо
		if(!$subject_rf || $subject_rf==0) $this->error('CANNOT_REALISE_SUBJECTRF');
	
		if(!$city) $this->error('CANNOT_REALISE_CITY');
		
		$tags=Array();
		$model=new Holes;		
		$model->USER_ID=$user->id;	
		$model->DATE_CREATED=time();
		$model->ADR_SUBJECTRF=$subject_rf;
		$model->ADR_CITY=trim($city);
		$model->ADDRESS=trim($address);
		if ($user->level > 50) $model->PREMODERATED=1;
		else $model->PREMODERATED=0;
		$model->LATITUDE=$latitude;
		$model->LONGITUDE=$longitude;
		$model->TYPE_ID=$typemodel->id;
		$model->COMMENT1=$comment;
		if (!$gibdd_id){
			$subjmodel=RfSubjects::model()->findByPk($subject_rf);
			if ($subjmodel) $model->gibdd_id=$subjmodel->gibdd->id;
			else $model->gibdd_id=0;
			}
		else $model->gibdd_id=$gibdd_id;
		
		if (!$model->upploadedPictures) $this->error('NO_FILES'); 
		
		$model->validate();
		if ($model->getError('upploadedPictures')) $this->getUploadError($model->getError('upploadedPictures'));  
		
			if($model->save() && $model->savePictures())
				$tags[]=CHtml::tag('callresult', array ('result'=>1, 'inserteddefectid'=>$model->ID), 'ok', true);
			else $this->error('CANNOT_ADD_DEFECT');			
		
		$this->renderXml($tags);
	}
	
	public function actionUpdate($id)
	{
	
		$model=$this->loadChangeModel($id);
		
		if($model->STATE!='fresh')	
			$this->error('UNAPPROPRIATE_METHOD');

		$address=Yii::app()->request->getParam('address');		
		$latitude=Yii::app()->request->getParam('latitude');		
		$longitude=Yii::app()->request->getParam('longitude');		 
		$comment=Yii::app()->request->getParam('comment');
		$type=Yii::app()->request->getParam('type');
		$deletefiles=Yii::app()->request->getParam('deletefiles');
		$gibdd_id=Yii::app()->request->getParam('gibdd_id');
		if ($type){
			$typemodel=HoleTypes::model()->find('alias="'.$type.'"');
			if (!$typemodel) $this->error('INCORRECT_TYPE');
			elseif (!$typemodel->published) $this->error('DEPRECATED_TYPE'); 
			}
		
		if ($address){
			$addressArr    = RfSubjects::model()->Address($address);
			$subject_rf = $addressArr['subject_rf'];
			$city       = $addressArr['city'];
			$address    = $addressArr['address'];
			// ворнинги, если надо
			if((!$subject_rf || !$city || !$address) && ($latitude && $longitude)){
				$addressArr    = RfSubjects::model()->AddressfromLatLng($latitude, $longitude, $this->mapkey);
					if ($addressArr) {
						$subject_rf = $addressArr['subject_rf'];
						$city       = $addressArr['city'];
						$address    = $addressArr['address'];	
					}
			}
			if(!$subject_rf) $this->error('CANNOT_REALISE_SUBJECTRF');	
			if(!$city) $this->error('CANNOT_REALISE_CITY');
		}
		
		$tags=Array();

		if ($address) {
		$model->ADR_SUBJECTRF=$subject_rf;
		$model->ADR_CITY=trim($city);
		$model->ADDRESS=trim($address);
		}
		if ($latitude) $model->LATITUDE=$latitude;
		if ($longitude) $model->LONGITUDE=$longitude;
		if ($type) $model->TYPE_ID=$typemodel->id;
		if ($comment) $model->COMMENT1=$comment;
		if ($deletefiles) $model->deletepict=$deletefiles;
		if ($gibdd_id) $model->gibdd_id=$gibdd_id;
		$model->validate();
		if ($model->getError('upploadedPictures')) $this->getUploadError($model->getError('upploadedPictures'));
			if($model->save() && $model->savePictures())
				$tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
			else $this->error('CANNOT_UPDATE_DEFECT');
		
		$this->renderXml($tags);
	}
	
	public $updateMethods=Array(
		'fresh'=>Array('update', 'delete', 'setinprogress', 'setfixed'),
		'inprogress'=>Array('revoke', 'setreplied', 'setfixed'),
		'fixed'=>Array('defix'),
		'achtung'=>Array('toprosecutor', 'setfixed'),
		'gibddre'=>Array('setreplied', 'setfixed'),
		'to_prosecutor'=>Array('revokep', 'setfixed'),
	);
	
	public function actionGetupdatemethods()
	{
		$tags=Array();			
		foreach ($this->updateMethods as $state=>$methods){
			$tags[]=CHtml::tag('state', array ('id'=>$state), false, false);
				foreach ($methods as $method)
					$tags[]=CHtml::tag('method', array ('name'=>$method), false, true);
			$tags[]=CHtml::closeTag('state');	
		}
		$this->renderXml($tags);
	}
	
	public function actionSetstate($id,$type)
	{
		$user=$this->auth();
		$model=$this->loadModel($id);
		$tags=Array();		
		switch($type)
		{
			case 'getupdatemethods':
			{
				$tags[]=CHtml::tag('state', array ('id'=>$model->STATE), false, false);
					foreach ($this->updateMethods[$model->STATE] as $method)
						$tags[]=CHtml::tag('method', array ('name'=>$method), false, true);
				$tags[]=CHtml::closeTag('state');		
				break;
			}			
			case 'setinprogress':
			{
				if ($model->makeRequest('gibdd')) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
				else $this->error('UNAPPROPRIATE_METHOD');
				break;
			}
			case 'revoke':
			{
				if ($model->updateRevoke()) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
				else $this->error('UNAPPROPRIATE_METHOD');
				break;
			}
			case 'setreplied':
			{
				$model->scenario='gibdd_reply';
				if($model->STATE!='inprogress' && $model->STATE!='achtung' && !$model->request_gibdd)	
					$this->error('UNAPPROPRIATE_METHOD');		
				$answer=new HoleAnswers;
				$answer->request_id=$model->request_gibdd->id;
				$answer->date=time();
				$answer->comment=Yii::app()->request->getParam('comment'); 
				if($answer->save()){
					if ($model->STATE=="inprogress" || $model->STATE=="achtung")
						$model->STATE='gibddre';
					$model->GIBDD_REPLY_RECEIVED=1;
					if (!$model->DATE_STATUS) $model->DATE_STATUS=time();
					if ($model->update()) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
					}
				else $this->error('CANNOT_UPDATE_DEFECT');	
				break;
			}
			case 'setfixed':
			{
				if (!$model->isUserHole && $user->level < 50){
				if ($model->STATE=='fixed' || !$model->request_gibdd || !$model->request_gibdd->answers)
					$this->error('UNAPPROPRIATE_METHOD');	
				}	
				elseif ($model->STATE=='fixed')
					$this->error('UNAPPROPRIATE_METHOD');		
				$model->scenario='fix';
				$model->STATE='fixed';
				$model->COMMENT2=Yii::app()->request->getParam('comment');
				$model->DATE_STATUS=time();
				if ($model->save() && $model->savePictures()) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
				else $this->error('CANNOT_UPDATE_DEFECT');
				break;
			}
			case 'defix':
			{
				if ($model->STATE!='fixed')
					$this->error('UNAPPROPRIATE_METHOD');	
				if ($model->isUserHole || $user->level >= 50){
					if ($model->updateSetinprogress()) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
					else $this->error('UNAPPROPRIATE_METHOD');	
				}	
				else $this->error('UNAPPROPRIATE_METHOD');						
				break;
			}
			case 'toprosecutor':
			{
				if ($model->makeRequest('prosecutor')) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
				else $this->error('UNAPPROPRIATE_METHOD');
				break;
			}
			case 'revokep':
			{
				if ($model->updateRevoke()) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
				else $this->error('UNAPPROPRIATE_METHOD');
				break;
			}
			case 'delete':
			{
				if ($model->isUserHole && $model->delete()) $tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
				else $this->error('UNAPPROPRIATE_METHOD'); 
				break;
			}
			case 'getgibddhead':
			{
				if ($model->gibdd) {
					$tags[]=CHtml::tag('gibddhead', array ('subjectid'=>$model->gibdd->subject->id, 'id'=>$model->gibdd->id), false, false);
						$tags[]=CHtml::tag('nominative', array ('post'=>$model->gibdd->post, 'gibdd'=>$model->gibdd->gibdd_name), CHtml::encode($model->gibdd->fio), true);
						$tags[]=CHtml::tag('dative', array ('post'=>$model->gibdd->post_dative), CHtml::encode($model->gibdd->fio_dative), true);
					$tags[]=CHtml::closeTag('gibddhead');
				}
				else $this->error('UNAPPROPRIATE_METHOD'); 
				break;
			}			
			case 'pdf_gibdd':
			{
				$attribs=Array(				
				'to'=>Yii::app()->request->getParam('to'),
				'from'=>Yii::app()->request->getParam('from'),
				'postaddress'=>Yii::app()->request->getParam('postaddress'),
				'address'=>Yii::app()->request->getParam('holeaddress') ? Yii::app()->request->getParam('holeaddress') : $model->ADDRESS,
				'comment'=>Yii::app()->request->getParam('comment'),
				'signature'=>Yii::app()->request->getParam('signature'),
				'pdf'=>true,
				);
				$this->makepdf($attribs, $model);
				Yii::app()->end();
				$tags[]=CHtml::tag('callresult', array ('result'=>1), 'ok', true);
				break;
			}
			case 'pdf_prosecutor':
			{
				$attribs=Array(
				'form_type'=>'prosecutor',
				'to'=>Yii::app()->request->getParam('to'),
				'from'=>Yii::app()->request->getParam('from'),
				'postaddress'=>Yii::app()->request->getParam('postaddress'),
				'address'=>Yii::app()->request->getParam('holeaddress') ? Yii::app()->request->getParam('holeaddress') : $model->ADDRESS,
				'comment'=>Yii::app()->request->getParam('comment'),
				'signature'=>Yii::app()->request->getParam('signature'),
				'gibdd'=>Yii::app()->request->getParam('gibdd'),
				'gibdd_reply'=>Yii::app()->request->getParam('gibddre'),
				'application_data'=>$model->request_gibdd ? date('d.m.Y',$model->request_gibdd->date_sent) : '',
				'pdf'=>true,
				);
				$this->makepdf($attribs, $model);
				Yii::app()->end();
				break;
			}
			default :
			{
				$this->error('UNAPPROPRIATE_METHOD'); 
				break;
			}
		}
		$this->renderXml($tags);
	}
	
	public function actionGeocode()
	{
		$user=$this->auth();
		$string=Yii::app()->request->getParam('geocode');
		if (!$string) $this->error('GEOCODE_EMPTY_REQUEST');
		$tags=Array();
		
				$c = curl_init('http://geocode-maps.yandex.ru/1.x/?format=xml&geocode='.urlencode($string).'&key='.$this->mapkey);
				ob_start();
				curl_exec($c);
				$out = explode("\n", ob_get_clean());
				$cinfo = curl_getinfo($c);
				unset($out[0]);
				curl_close($c);
				if
				(
					$cinfo['http_code'] != 200
					|| !sizeof($out)
					|| substr($cinfo['content_type'], 0, 8) != 'text/xml'
					|| !$cinfo['size_download']
				)
				{
					$this->error('GEOCODE_ERROR');
					break;
				}
				$tags[]=CHtml::tag('geocode', array ('result'=>1), 'ok', false);
				foreach($out as $str)
				{
					$tags[]="\t\t".str_replace('  ', "\t", $str)."\n";
				}
				$tags[]=CHtml::closeTag('geocode'); 		
			
		$this->renderXml($tags);		
	}
	
	public function actionGetgibddheadbyregion()
	{
		$user=$this->auth();
		$region_id=(int)Yii::app()->request->getParam('region_id');		
		$model=RfSubjects::model()->findByPk($region_id);
		if (!$model) $this->error('NOT_FOUND');
		$tags=Array();
		if ($model->gibdd){
			$tags[]=CHtml::tag('gibdd', array ('type'=>'regional', 'subjectid'=>$model->id, 'id'=>$model->gibdd->id), false, false);
				$tags[]=CHtml::tag('gibdditem', array ('address'=>$model->gibdd->address, 'tel'=>$model->gibdd->tel_degurn), CHtml::encode($model->gibdd->gibdd_name), true);
				$tags[]=CHtml::tag('nominative', array ('post'=>$model->gibdd->post, 'gibdd'=>$model->gibdd->gibdd_name), CHtml::encode($model->gibdd->fio), true);
				$tags[]=CHtml::tag('nominative', array ('dative'=>$model->gibdd->post_dative), CHtml::encode($model->gibdd->fio_dative), true);
			$tags[]=CHtml::closeTag('gibdd');		
		}
		foreach ($model->gibdd_local as $gibdd){
			$tags[]=CHtml::tag('gibdd', array ('type'=>'local', 'subjectid'=>$model->id, 'id'=>$gibdd->id), false, false);
				$tags[]=CHtml::tag('gibdditem', array ('address'=>$gibdd->address, 'tel'=>$gibdd->tel_degurn, 'lat'=>$gibdd->lat, 'lng'=>$gibdd->lng), CHtml::encode($gibdd->gibdd_name), true);
				$tags[]=CHtml::tag('nominative', array ('post'=>$gibdd->post, 'gibdd'=>$gibdd->gibdd_name), CHtml::encode($gibdd->fio), true);
				$tags[]=CHtml::tag('nominative', array ('dative'=>$gibdd->post_dative), CHtml::encode($gibdd->fio_dative), true);
			$tags[]=CHtml::closeTag('gibdd');		
		}	
		$this->renderXml($tags);		
	}
	
	public function makepdf($attribs, $model)
	{
			$request=new HoleRequestForm;
			if($attribs)
			{
				$request->attributes=$attribs;
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
					foreach($model->pictures_fresh as $picture)
					{
						$_images[] = $picture->original;
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
					foreach($model->pictures_fresh as $picture)
					{
						$_images[] = $_SERVER['DOCUMENT_ROOT'].$picture->original;
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
	
	
	public function auth()
	{
		if (Yii::app()->user->isGuest){
			$model=new UserGroupsUser('login');
			$loginmode='regular';
			$model->username=Yii::app()->request->getParam('login');
			$model->password=Yii::app()->request->getParam('password');
			if (Yii::app()->request->getParam('passwordhash')) {
				$model->password=Yii::app()->request->getParam('passwordhash');
				$loginmode='fromHash';
				}
			$model->rememberMe=0;		
			if ($model->validate() && $model->login($loginmode)) return Yii::app()->user;
			else {
				$this->error('AUTHORIZATION_REQUIRED');
				}
		}
		else return Yii::app()->user; 
	}
	
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error){
       		if ($error['code']==404) $this->error('NOT_FOUND');
      		else $this->error('INTERNAL');
       	}	 
	}
	
	public function error($str)
	{
		$this->renderXml($this->errors[$str]);
		Yii::app()->end();
		
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
			$this->error('NOT_FOUND');
		return $model;
	}
	
	//Лоадинг модели для пользовательских изменений
	public function loadChangeModel($id)
	{
		$user=$this->auth();
		$model=Holes::model()->findByPk($id);
		if($model===null)
			$this->error('NOT_FOUND');	
		elseif($model->USER_ID!=$user->id)	
			$this->error('UNAPPROPRIATE_METHOD');
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
