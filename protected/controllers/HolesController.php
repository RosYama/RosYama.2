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
				'actions'=>array('index','view', 'findSubject', 'findCity', 'map', 'flushcashe', 'ajaxMap', 'cronDaily'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('add','update', 'personal','personalDelete','request','requestForm','sent','notsent','gibddreply', 'fix', 'defix', 'prosecutorsent', 'prosecutornotsent','delanswerfile','myarea', 'territorialGibdd', 'delpicture','selectHoles','sentMany','review', 'selected', 'addFixedFiles', 'approveFixedPicture'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete', 'moderate','moderPhotoFix'),
				'groups'=>array('root', 'admin', 'moder'), 
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin', 'itemsSelected'),
				'groups'=>array('root',), 
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionFlushcashe()
	{
		Yii::app()->cache->flush();
		Yii::app()->user->setFlash('user','Кеш чист!');
		$this->redirect(Array('personal'));
	}	
	
	public function actionCronDaily($type){	
		
		set_time_limit(0);
		
		$logmodel=HoleCronLog::model()->findByAttributes(Array('type'=>$type), 'time_finish >= '.CDateTimeParser::parse(date('Y-m-d'), 'yyyy-MM-dd'));		
		
		if (!$logmodel){
			$logmodel=new HoleCronLog;
			if ($type=="achtung-notifications"){		
				$logmodel->type=$type;
				$logmodel->time_finish=time();
				$logmodel->save();	
				//отмечаем ямы как просроченные
				$holes=Holes::model()->findAll(Array('condition'=>'t.STATE in ("inprogress", "achtung") AND t.DATE_SENT > 0'));
				foreach ($holes as $hole){
					$WAIT_DAYS = 38 - ceil((time() - $hole->DATE_SENT) / 86400);
					if ($WAIT_DAYS < 0 && $hole->STATE == 'inprogress') {
						$hole->STATE = 'achtung';
						$hole->update();
					}
					elseif ($hole->STATE == 'achtung' && $WAIT_DAYS > 0){
						$hole->STATE = 'inprogress';
						$hole->update();			
					}					
				}
				//echo count($holes);

				//Находим пользователей с просроченными запросами
				$users=1;
				$limit=200;
				$ofset=0;
				while ($users){
					$users=UserGroupsUser::model()->findAll(Array(
							'select'=>'*, t.id as notUseAfrefind',
							'condition'=>'relProfile.send_achtung_notifications=1 AND t.email != ""',
							'with'=>Array(
									'relProfile', 
									'requests'=>Array(
										'with'=>Array('answer', 'hole'=>Array('with'=>Array('type', 'pictures_fresh'))),
										'condition'=>'requests.date_sent < '.(time()-(60 * 60 * 24 * 28)).' AND requests.type="gibdd" AND (requests.notification_sended=0 OR requests.notification_sended < '.(time()-(60 * 60 * 24 * 30)).' ) AND hole.STATE NOT IN ("fixed", "prosecutor") AND answer.request_id IS NULL',
									),
									'relUserGroupsGroup',
								),
							'limit'=>$limit,
							'offset'=>$ofset,
							));
					
					foreach ($users as $user){					
						$holes=Array();
						$i=0;
						foreach ($user->requests as $request){					
								$WAIT_DAYS = 38 - ceil((time() - $request->date_sent) / 86400);
								if ($WAIT_DAYS < 0) {
									$holes[$i]=$request->hole;						
									$holes[$i]->PAST_DAYS=abs($WAIT_DAYS);
									$i++;
									$request->notification_sended=time();
									$request->update();
									
									}												

						}
						if ($holes){
							$headers = "MIME-Version: 1.0\r\nFrom: \"Rosyama\" <".Yii::app()->params['adminEmail'].">\r\nReply-To: ".Yii::app()->params['adminEmail']."\r\nContent-Type: text/html; charset=utf-8";
							Yii::app()->request->baseUrl=Yii::app()->request->hostInfo;
							$mailbody=$this->renderPartial('/ugmail/achtung_notification', Array('user'=>$user, 'holes'=>$holes),true);
							//echo $mailbody; die();
							//$user->email
							echo 'Напоминание на '.count($holes).'ям, отправлено пользователю '.$user->username.'<br />';
							mail($user->email,"=?utf-8?B?" . base64_encode('Истекло время ожидания ответа от ГИБДД') . "?=",$mailbody,$headers);
						}
						unset ($holes);
					}
					$ofset+=$limit;
					//if ($ofset > 2000) break;
				}			
		
			$this->render('/site/index');
			}
		}	
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
        $model=$this->loadModel($id);
        
        
        
		$this->render('view',array(
			'hole'=>$model,

		));
	}
	
	public function actionAddFixedFiles($id)
	{
		$model=$this->loadModel($id);
		if(isset($_POST['Holes']))
		{			
			$model->scenario="addFixedFiles";
			if ($model->savePictures()) Yii::app()->user->setFlash('user', 'Файлы загружены. После одобрания пользователем, загрузившим эту яму или модератором яма получит статус "устранено"');
			
			//Отправляем уведомление хозяину ямы
			$currentUser=Yii::app()->user->userModel;
			$pictures=HolePictures::model()->findAll('hole_id='.$model->ID.' AND type="fixed" AND premoderated=0 AND user_id='.$currentUser->id);
			if ($pictures){
				$headers = "MIME-Version: 1.0\r\nFrom: \"Rosyama\" <".Yii::app()->params['adminEmail'].">\r\nReply-To: ".$currentUser->email ? $currentUser->email : Yii::app()->params['adminEmail']."\r\nContent-Type: text/html; charset=utf-8";
				$user=$model->user;
				Yii::app()->request->baseUrl=Yii::app()->request->hostInfo;
				$mailbody=$this->renderPartial('/ugmail/fixed_pictures_notification', Array('user'=>$user, 'currentUser'=>$currentUser, 'pictures'=>$pictures, 'hole'=>$model),true);
				//echo $mailbody; die();
				//$user->email
				mail($user->email,"=?utf-8?B?" . base64_encode('Новые фотографии исправленной ямы') . "?=",$mailbody,$headers);
			}
			
		}
		
		$this->redirect(Array('view','id'=>(int)$id));
		
	}	
	
	public function actionApproveFixedPicture($id, $pictid)
	{
		$model=$this->loadChangeModel($id);		
		
		$picmodel=HolePictures::model()->findByPk((int)$pictid);
		
		if ($picmodel){
			$picmodel->premoderated=1;
			if ($picmodel->update()){
				if (!$model->user_fix){
					$model->scenario='fix';
					$model->STATE='fixed';
					$model->DATE_STATUS=time();
					if ($model->save()) Yii::app()->user->setFlash('user', 'Статус ямы успешно изменен');
				}
			}
		}
		
		$this->redirect(Array('view','id'=>(int)$id));
		
	}		
	
	public function actionReview($id)
	{
		$this->redirect(Array('view','id'=>(int)$id));
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
			
			$model->ADR_CITY=trim($model->ADR_CITY);
			
			if (Yii::app()->user->level > 50) $model->PREMODERATED=1;
			else $model->PREMODERATED=0;
			
			if ($model->gibdd_id){
				$subj=$model->gibdd->subject->id;
				if($subj) $model->ADR_SUBJECTRF=$subj;
			}
			else {
				$subj=RfSubjects::model()->SearchID(trim($model->STR_SUBJECTRF));
				if($subj) $model->ADR_SUBJECTRF=$subj;
				else $model->ADR_SUBJECTRF=0;
			}
			
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
	
	
	//Список ГИБДД возле ямы
	public function actionTerritorialGibdd()
	{
		if(isset($_POST['Holes']))
		{
			$model=new Holes;
			$model->attributes=$_POST['Holes'];
			$subj=RfSubjects::model()->SearchID(trim($model->STR_SUBJECTRF));
			if($subj) $model->ADR_SUBJECTRF=$subj;
			else $model->ADR_SUBJECTRF=0;
			
			$data=CHtml::listData($model->territorialGibdd,'id','gibdd_name');
		    foreach($data as $value=>$name)
		    {
		        echo CHtml::tag('option',
		                   array('value'=>$value),CHtml::encode($name),true);
		    }
			
		}
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

			if ($model->gibdd_id){
				$subj=$model->gibdd->subject->id;
				if($subj) $model->ADR_SUBJECTRF=$subj;
			}
			else if ($model->STR_SUBJECTRF){
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
	
	
	public function actionGibddreply($id=null, $holes=null)
	{
		$this->layout='//layouts/header_user';
		$count=0;
		$firstAnswermodel=Array();
		$models=Array();
		if (!$holes){
		$model=$this->loadModel($id);
		$model->scenario='gibdd_reply';
		if(!$model->request_gibdd)	
			throw new CHttpException(403,'Доступ запрещен.');
		$models[]=$model;
		}	
		else $models=Holes::model()->findAllByPk(explode(',',$holes));
		foreach ($models as $i=>$model){
			if(!$model->request_gibdd) {unset ($models[$i]); continue;}
			$answer=new HoleAnswers;
			if (isset($_GET['answer']) && $_GET['answer'])
				$answer=HoleAnswers::model()->findByPk((int)$_GET['answer']);
				
			$answer->request_id=$model->request_gibdd->id;
	
				if(isset($_POST['HoleAnswers']))
				{					
					$answer->attributes=$_POST['HoleAnswers'];
					//if (isset($_POST['HoleAnswers']['results'])) $answer->results=$_POST['HoleAnswers']['results'];
					$answer->request_id=$model->request_gibdd->id;
					$answer->date=time();
					if ($firstAnswermodel) $answer->firstAnswermodel=$firstAnswermodel;
					if($answer->save()){
						if ($model->STATE=="inprogress" || $model->STATE=="achtung") $model->STATE='gibddre';
						$model->GIBDD_REPLY_RECEIVED=1;
						$model->DATE_STATUS=time();
						if ($model->update()){					
							if ($count==0) $firstAnswermodel=$answer;
							$count++;
							$links[]=CHtml::link($model->ADDRESS,Array('view','id'=>$model->ID));						
							if (!$holes) $this->redirect(array('view','id'=>$model->ID));						
							}
						}					
					
				}
				else {
					if (!$answer->isNewRecord) $answer->results=CHtml::listData($answer->results,'id','id');
				}
		}
		
		if ($holes && $count) {
					if($count) Yii::app()->user->setFlash('user', 'Успешная загрузка ответа ГИБДД на ямы: <br/>'.implode('<br/>',$links).'<br/><br/><br/>');
					else Yii::app()->user->setFlash('user', 'Произошла ошибка! Ни одного ответа не загружено');
					$this->redirect(array('personal')); 
		}	

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
		
		$this->render('gibddreply',array(
			'models'=>$models,
			'answer'=>$answer,
			'jsplacemarks'=>'',
		));
	}
	
	public function actionFix($id)
	{
		$this->layout='//layouts/header_user';
		
		$model=$this->loadModel($id);
		if (!$model->isUserHole && Yii::app()->user->level < 50){
			if ($model->STATE=='fixed' || !$model->request_gibdd || !$model->request_gibdd->answers || $model->user_fix){
				if ($model->STATE=='fixed' || $model->user_fix) throw new CHttpException(403,'Доступ запрещен.');
				else throw new CHttpException(403,'Для отметки дефекта как исправленного необходимо загрузить ответ из ГИБДД. Если ответа из ГИБДД у вас нет, обратитесь к пользователю, добавившему этот дефект, для проставления соответствующей отметки.');
			}
				
		}		
		elseif ($model->STATE=='fixed' && $model->user_fix)
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
				if ($model->save() && $model->savePictures()){					
					$this->redirect(array('view','id'=>$model->ID));
					}
		}

		$this->render('fix_form',array(
			'model'=>$model,	
			'newimage'=>new PictureFiles
		));
	}	
	
	public function actionDefix($id)
	{
		$model=$this->loadModel($id);
		if (!$model->user_fix && Yii::app()->user->level < 80)
			throw new CHttpException(403,'Доступ запрещен.');
		
		if ($model->user_pictures_fixed) Yii::app()->user->setFlash('user', 'Для того чтобы анулировать факт исправления, сначала необходимо удалить загруженные Вами фотографии!');
		
		$model->updateSetinprogress();
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}	

	//удаление ямы админом или модером
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest && (isset($_POST['id']) || (isset($_POST['DELETE_ALL']) && $_POST['DELETE_ALL'])))
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
				$model->deletor_id=Yii::app()->user->id;
				$model->deleted=1;
				$model->update();
			}
			else {
				$holes=Holes::model()->findAll('id IN ('.$_POST['DELETE_ALL'].')');
				$ok=0;
				foreach ($holes as $model){
					$model->deletor_id=Yii::app()->user->id;
					$model->deleted=1;
					if ($model->update()) $ok++;
					}
				if ($ok==count($holes))  echo 'ok';
			}			

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect($_SERVER['HTTP_REFERER']);
		}
		elseif (Yii::app()->user->groupName=='root'){
			$model=Holes::model()->findByPk((int)$_GET['id']);
			if ($model) $model->delete();
			}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	//удаление ямы пользователем
	public function actionPersonalDelete($id)
	{
			$model=$this->loadChangeModel($id);				
			if (!$model->isUserHole && Yii::app()->user->level<=90){
				$model->deleted=1;
				$model->update();
			}
			else $model->delete();			
			
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('personal'));		
	}	
	
	//форма ГИБДД
	public function actionRequestForm($id, $type, $holes)
	{
		if ($id){
			$gibdd=GibddHeads::model()->findByPk((int)$id);
			$holemodel=Holes::model()->findAllByPk(explode(',',$holes));
			if ($type=='gibdd') 
				$this->renderPartial('_form_gibdd_manyholes',Array('holes'=>$holemodel, 'gibdd'=>$gibdd));
		}
		//else echo "Выбирите отдел ГИБДД";
	}	

	//генерация запросов в ГИБДД
	public function actionRequest($id=null)
	{			
			if ($id) $model=$this->loadModel($id);				
			else $model=new Holes;
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
					'date1.day'   => date('d', $model->DATE_CREATED ? $model->DATE_CREATED : time()),
					'date1.month' => date('m', $model->DATE_CREATED ? $model->DATE_CREATED : time()),
					'date1.year'  => date('Y', $model->DATE_CREATED ? $model->DATE_CREATED : time()),
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
					if (!$request->holes)
						$HT->gethtml
						(
							$request->form_type ? $request->form_type : $model->type,
							$_data,
							$_images
						);
					else {
						$HT->models=Holes::model()->findAllByPk($request->holes);
							$HT->gethtml
							(
								'gibdd',
								$_data,
								Array(),
								$request->printAllPictures
							);
						}
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
					if (!$request->holes)
						$PDF->getpdf
						(
							$request->form_type ? $request->form_type : $model->type,
							$_data,
							$_images
						);
					else {
						$PDF->models=Holes::model()->findAllByPk($request->holes);
							$PDF->getpdf
							(
								'gibdd',
								$_data,
								Array(),
								$request->printAllPictures
							);
						}
				}
			}		
	}		

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		
		$this->layout='//layouts/header_default';
		
		//Если нет таблиц в базе редиректим на контроллер миграции
		if(Holes::getDbConnection()->getSchema()->getTable(Holes::tableName())===null)
			$this->redirect(array('migration/index'));
	
		$model=new Holes('search');		
		
		$model->unsetAttributes();  // clear any default values
		$model->PREMODERATED=1;
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
		$dataProvider=$model->search();	
		$this->render('index',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}
	
	public function actionModerPhotoFix()
	{
		
		$this->layout='//layouts/header_default';
	
		$model=new Holes('search');		
		
		$model->unsetAttributes();  // clear any default values
		$model->PREMODERATED=1;
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
		$dataProvider=$model->search(true);	
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
				$model->premoderator_id=Yii::app()->user->id;
				if ($model->update()) echo 'ok';
				}
			elseif (isset($_GET['ajax']) && $_GET['ajax']=='holes-grid'){
				$model->PREMODERATED=0;
				if ($model->update()) echo 'ok';	
			}
		}
		else {
			$holes=Holes::model()->findAll('id IN ('.$_GET['PREMODERATE_ALL'].')');
			$ok=0;
			foreach ($holes as $model)
			if (!$model->PREMODERATED) {
				$model->PREMODERATED=1;
				$model->premoderator_id=Yii::app()->user->id;
				if ($model->update()) $ok++;
				}
			if (isset($_GET['ajax']) && $ok==count($holes))  echo 'ok';
			else $this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : array('index'));
		}
	}
	
	public function actionSent($id)
	{
		$model=$this->loadModel($id);
		$model->makeRequest('gibdd');
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$model->ID));
	}
	
	public function actionSentMany($holes)
	{		
		$holesmodels=Holes::model()->findAllByPk(explode(',',$holes));
		$count=0;
		$links=Array();
		foreach ($holesmodels as $model){
			if ($model->makeRequest('gibdd')) {
				$count++;
				$links[]=CHtml::link($model->ADDRESS,Array('view','id'=>$model->ID));
				}
		}		
		if($count) Yii::app()->user->setFlash('user', 'Успешное изменение статуса ям: <br/>'.implode('<br/>',$links).'<br/><br/><br/>');
		else Yii::app()->user->setFlash('user', 'Произошла ошибка! Ни одной ямы не изменено');
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : array('personal'));
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
	
	//удаление изображения
	public function actionDelpicture($id)
	{
			$picture=HolePictures::model()->findByPk((int)$id);
			
			if (!$picture)
				throw new CHttpException(404,'The requested page does not exist.');
				
			if ($picture->user_id!=Yii::app()->user->id && Yii::app()->user->level < 80 && $picture->hole->USER_ID!=Yii::app()->user->id)
				throw new CHttpException(403,'Доступ запрещен.');
				
			$picture->delete();			
			
			if(!isset($_GET['ajax']))
				$this->redirect(array('view','id'=>$picture->hole->ID));
		
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
		$user=$this->user;
		
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/holes_list.css');        
        $cs->registerCssFile('/css/hole_view.css');
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'holes_selector.js'));
		$cs->registerScriptFile('http://www.vertstudios.com/vertlib.min.js');        
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'StickyScroller.min.js'));
		$cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'GetSet.js'));
		$holes=Array();
		$all_holes_count=0;		
			
		$this->render('personal',array(
			'model'=>$model,
			'user'=>$user,
			'dataProvider'=>$model->userSearch(),
		));
	}
	
	public function actionSelectHoles($del=false)
	{
		$gibdds=Array();
		$del=filter_var($del, FILTER_VALIDATE_BOOLEAN);	
		if (isset($_POST['holes'])) $holestr=$_POST['holes'];
		else $holestr=''; 
		if ($holestr=='all' && $del) {
			Yii::app()->user->setState('selectedHoles', Array());
			//Yii::app()->end();
			}
		else{	
			$holes=explode(',',$holestr);
			for ($i=0;$i<count($holes);$i++) {$holes[$i]=(int)$holes[$i]; if(!$holes[$i]) unset($holes[$i]);}
			
			$selected=Yii::app()->user->getState('selectedHoles', Array());
			if (!$del){
				$newsel=array_diff($holes, $selected);
				$selected=array_merge($selected, $newsel);
			}
			else {	
				$newsel=array_intersect($selected, $holes);
				foreach ($newsel as $key=>$val) unset($selected[$key]);
			}
			Yii::app()->user->setState('selectedHoles', $selected);
			if ($selected) $gibdds=GibddHeads::model()->with('holes')->findAll('holes.id IN ('.implode(',',$selected).')');

		}
		$this->renderPartial('_selected', Array('gibdds'=>$gibdds,'user'=>Yii::app()->user->userModel));
		
		//print_r(Yii::app()->user->getState('selectedHoles'));
	}	
	
	public function actionSelected($id)
	{
		//$this->layout='//layouts/header_user';
		
		$user=Yii::app()->user;
		$list=UserSelectedLists::model()->findByPk((int)$id);
		
		if(!$list || $list->user_id!=$user->id)	
			throw new CHttpException(403,'Доступ запрещен.');
			
		if (isset($_POST['gibdd_change_id']) && $_POST['gibdd_change_id']){
			$newgibdd=GibddHeads::model()->findByPk((int)$_POST['gibdd_change_id']);
			if ($newgibdd && $newgibdd->subject_id==$list->gibdd->subject_id){
				foreach ($list->holes as $hole){
					$hole->gibdd_id=$newgibdd->id;
					$hole->update();
				}
				$list->gibdd_id=$newgibdd->id;
				$list->update();
				$this->refresh();
			}
		}
			
			
		$model=new Holes('search');
		$model->unsetAttributes();
		$model->showUserHoles=3;
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
		
		$model->selecledList=$list->id;
		
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/holes_list.css');        
        $cs->registerCssFile('/css/hole_view.css');
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'holes_selector.js'));
		$cs->registerScriptFile('http://www.vertstudios.com/vertlib.min.js');        
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'StickyScroller.min.js'));
		$cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'GetSet.js'));
		$holes=Array();
		$all_holes_count=0;		
		
			
		$this->render('selected', Array('model'=>$model, 'list'=>$list,'user'=>Yii::app()->user));
		
		
		
		//print_r(Yii::app()->user->getState('selectedHoles'));
	}	
	
	public function actionMyarea()
	{
		$user=Yii::app()->user;
		$area=$user->userModel->hole_area;
		if (!$area)	$this->redirect(array('/profile/myarea'));
		
		$this->layout='//layouts/header_user';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		
		if(isset($_POST['Holes']) || isset($_GET['Holes']))
			$model->attributes=isset($_POST['Holes']) ? $_POST['Holes'] : $_GET['Holes'];
		
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/holes_list.css');
		$cs->registerCssFile('/css/hole_view.css');
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'holes_selector.js'));
		$cs->registerScriptFile('http://www.vertstudios.com/vertlib.min.js');        
        $cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'StickyScroller.min.js'));
		$cs->registerScriptFile(CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'StickyScroller'.DIRECTORY_SEPARATOR.'GetSet.js'));              
		
		$holes=Array();
		$all_holes_count=0;		
					
		$this->render('myarea',array(
			'model'=>$model,
			'user'=>$user,
			'area'=>$area,
			'dataProvider'=>$model->areaSearch($user),
		));
	}
	
	public function actionMap($userid=null)
	{
		//$this->layout='//layouts/header_default';
		
		if ($userid) {
			$usermodel=$this->loadUserModel($userid);
			if (!$usermodel->getParam('showMyarea'))
				throw new CHttpException(403,'Доступ запрещен.');
			if (!$usermodel->hole_area)
				throw new CHttpException(404,'Зона наблюдения пользователя не определена');	
			}
		else $usermodel=null;
		
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Holes']))
			$model->attributes=$_POST['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
			
		$this->render('map',array(
			'model'=>$model,
			'types'=>HoleTypes::model()->findAll(Array('condition'=>'t.published=1', 'order'=>'ordering')),
			'usermodel'=>$usermodel,
		));
	}
	
	public function actionAjaxMap()
	{
		$criteria=new CDbCriteria;
		/// Фильтрация по масштабу позиции карты
		
		if (isset($_GET['zoom'])) $ZOOM=$_GET['zoom'];
		else $ZOOM=14;
		
		if ($ZOOM < 3) { $_GET['left']=-190; $_GET['right']=190;}

		if ((!isset ($_GET['bottom']) || !isset ($_GET['left']) || !isset ($_GET['right']) || !isset ($_GET['top'])) && !isset($_GET['user_id'])) Yii::app()->end();
		
		if  (!isset($_GET['user_id'])){
			if (isset ($_GET['bottom'])) $criteria->addCondition('LATITUDE > '.(float)$_GET['bottom']);
			if (isset ($_GET['left'])) $criteria->addCondition('LONGITUDE > '.(float)$_GET['left']);	 	
			if (isset ($_GET['right'])) $criteria->addCondition('LONGITUDE < '.abs((float)$_GET['right']));		
			if (isset ($_GET['top'])) $criteria->addCondition('LATITUDE < '.abs((float)$_GET['top']));	
		}
		else {
			$usr=UserGroupsUser::model()->findByPk((int)$_GET['user_id']);			
			$area=$usr->hole_area;		
			foreach ($area as $shape){
				$cond='LONGITUDE >= '.$shape->corners['left']
				.' AND LONGITUDE <= '.$shape->corners['right']
				.' AND LATITUDE >= '.$shape->corners['bottom']
				.' AND LATITUDE <= '.$shape->corners['top'];					
				$criteria->addCondition($cond,'OR');			
				}		
		
			$notPolygonHolesIds=Holes::model()->findPkeysNotInAreaByUser($usr);
			if ($notPolygonHolesIds) $criteria->addNotInCondition('t.ID',$notPolygonHolesIds);	
		}
		
		if (isset ($_GET['exclude_id']) && $_GET['exclude_id']) $criteria->addCondition('ID != '.(int)$_GET['exclude_id']); 
		if (!Yii::app()->user->isModer) $criteria->compare('PREMODERATED',1);
	

		/// Фильтрация по типу ямы
		if(isset($_GET['Holes']['type']) && $_GET['Holes']['type'])
		{
			$criteria->addInCondition('TYPE_ID', $_GET['Holes']['type']);
		}
		
		$criteria->with=Array('type');
		
		if(isset($_GET['Holes']['STATE']))
			$criteria->compare('t.STATE',$_GET['Holes']['STATE'],true);	
		if(isset($_GET['Holes']['TYPE_ID']))	
			$criteria->compare('t.TYPE_ID',$_GET['Holes']['TYPE_ID'],false);
		if(isset($_GET['Holes']['gibdd_id']))
			$criteria->compare('t.gibdd_id',$_GET['Holes']['gibdd_id'],false);
		if(isset($_GET['Holes']['archive']))
			$criteria->compare('t.archive',Array($_GET['Holes']['archive'],0),false);
		
		$criteria->compare('t.deleted',0);
		
		$userid=Yii::app()->user->id;
		if (isset($_GET['Holes']['showUserHoles'])) $showUserHoles=$_GET['Holes']['showUserHoles'];
		else $showUserHoles=0; 
		
		if ($showUserHoles==1) $criteria->compare('t.USER_ID',$userid,false);
		elseif ($showUserHoles==2) {
			$criteria->with=Array('type', 'requests');
			$criteria->addCondition('t.USER_ID!='.$userid);
			$criteria->compare('requests.user_id',$userid,true);
			$criteria->together=true;
			}	
		
		$markers = Holes::model()->findAll($criteria);	
		
		
		if ($ZOOM >=14) $ZOOM=30;
				
		$singleMarkers = array();
		$clusterMarkers = array();
		
		// Minimum distance between markers to be included in a cluster, at diff. zoom levels
		$DISTANCE = (7000000 >> $ZOOM) / 100000;
		
		// Loop until all markers have been compared.
		while (count($markers)) {
			$marker  = array_pop($markers);
			$cluster = array();
		
			// Compare against all markers which are left.
			foreach ($markers as $key => $target) {
				$pixels = abs($marker->LONGITUDE-$target->LONGITUDE) + abs($marker->LATITUDE-$target->LATITUDE);
		
				// If the two markers are closer than given distance remove target marker from array and add it to cluster.
				if ($pixels < $DISTANCE) {
					unset($markers[$key]);
					$cluster[] = $target;
				}
			}
		
			// If a marker has been added to cluster, add also the one we were comparing to.
			if (count($cluster) > 0) {
				$cluster[] = $marker;
				$clusterMarkers[] = $cluster;
			} else {
				$singleMarkers[] = $marker;
			}
		}
		
		
		$markers=Array();
		foreach($singleMarkers as &$hole)
		{
			if(!isset($_REQUEST['skip_id']) || $_REQUEST['skip_id'] != $hole['ID'])
			{
				$markers[]=Array('id'=>$hole->ID, 'type'=>$hole->type->alias, 'lat'=>$hole->LONGITUDE, 'lng'=>$hole->LATITUDE, 'state'=>$hole->STATE);				
			}
		}
		
		$clusters=Array();
		foreach($clusterMarkers as $markerss)
		{
			$lats=Array();
			$lngs=Array();
				foreach($markerss as &$hole)
					{
						$lats[]=$hole->LONGITUDE;
						$lngs[]=$hole->LATITUDE;
					}
			sort($lats);
			sort($lngs);
			$center_lat=($lats[0]+$lats[count($lats)-1])/2;
			$center_lng=($lngs[count($lngs)-1]+$lngs[0])/2;
			
			
				$clusters[]=Array('count'=>count($markerss), 				
				'lat'=>$center_lat, 'lng'=>$center_lng, 
				);				
				
		}
		echo $_GET['jsoncallback'].'({"clusters": '.CJSON::encode($clusters).', "markers": '.CJSON::encode($markers).' })';
		
		
		Yii::app()->end();		
		
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		
		if (isset($_GET['pageSize'])) {
			Yii::app()->user->setState('pageSize',(int)$_GET['pageSize']);
			unset($_GET['pageSize']);  // would interfere with pager and repetitive page size change
		}
		
		$this->layout='//layouts/header_user';
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Holes']))
			$model->attributes=$_GET['Holes'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	public function actionItemsSelected()
	{
	if (isset ($_POST['submit_mult']) && isset($_POST['itemsSelected'])) {
		if ($_POST['submit_mult']=='Удалить'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) $model->delete();
			}
		}

		if ($_POST['submit_mult']=='Отмодерировать'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) {
				$model->PREMODERATED=1;
				$model->update();
				}
			}
		}

		if ($_POST['submit_mult']=='Демодерировать'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) {
				$model->PREMODERATED=0;
				$model->update();
				}
			}
		}
		
		if ($_POST['submit_mult']=='В архив'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) {
				$model->archive=1;
				$model->update();
				}
			}
		}
		
		if ($_POST['submit_mult']=='Вытащить из архива'){
			foreach ( $_POST['itemsSelected'] as $id){
				$model=Holes::model()->findByPk((int)$id);
				if ($model) {
				$model->archive=0;
				$model->update();
				}
			}
		}
		
    }
		if (!isset($_GET['ajax'])) $this->redirect($_SERVER['HTTP_REFERER']);
	}	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Holes::model()->findByPk($id);
		if($model===null || ($model->deleted && Yii::app()->user->level < 95))
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function loadUserModel($id, $scenario = false)
	{
		$model=UserGroupsUser::model()->findByPk((int)$id);
		//if($model===null || ($model->relUserGroupsGroup->level > Yii::app()->user->level && !UserGroupsConfiguration::findRule('public_profiles')))
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		if ($scenario)
			$model->setScenario($scenario);
		return $model;
	}	
	
	//Лоадинг модели для пользовательских изменений
	public function loadChangeModel($id)
	{
		$model=Holes::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		elseif(!$model->IsUserHole && !Yii::app()->user->level>80)	
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
