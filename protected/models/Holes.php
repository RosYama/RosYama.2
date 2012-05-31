<?php

/**
 * This is the model class for table "{{holes}}".
 *
 * The followings are the available columns in table '{{holes}}':
 * @property string $ID
 * @property string $USER_ID
 * @property double $LATITUDE
 * @property double $LONGITUDE
 * @property string $ADDRESS
 * @property string $STATE
 * @property string $DATE_CREATED
 * @property string $DATE_SENT
 * @property string $DATE_STATUS
 * @property string $COMMENT1
 * @property string $COMMENT2
 * @property string $TYPE_ID
 * @property string $ADR_SUBJECTRF
 * @property string $ADR_CITY
 * @property string $COMMENT_GIBDD_REPLY
 * @property integer $GIBDD_REPLY_RECEIVED
 * @property integer $PREMODERATED
 * @property string $DATE_SENT_PROSECUTOR
 */
class Holes extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Holes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public $WAIT_DAYS; 	
	public $PAST_DAYS;	
	public $NOT_PREMODERATED;	
	public $STR_SUBJECTRF;	
	public $deletepict=Array();
	public $counts;
	public $state_to_filter;
	public $time;
	public $limit;
	public $offset=0;
	public $type_alias;
	public $showUserHoles;
	public $username;
	public $selecledList;
	public $keys=Array();
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{holes}}';
	}
	
	public $ADR_CITY='Город';
	
	public function getParams(){
		return array(
					'big_sizex'      => 1024,
					'big_sizey'      => 1024,
					'medium_sizex'   => 600,
					'medium_sizey'   => 450,
					'small_sizex'    => 240,
					'small_sizey'    => 160,
					'premoderated'   => 0,
					'min_delay_time' => 60
		);
	}	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('USER_ID, ADDRESS, DATE_CREATED, TYPE_ID, gibdd_id', 'required'),
			array('LATITUDE, LONGITUDE', 'required', 'message' => 'Поставьте метку на карте двойным щелчком мыши!'),	
			array('GIBDD_REPLY_RECEIVED, PREMODERATED, TYPE_ID, NOT_PREMODERATED, archive', 'numerical', 'integerOnly'=>true),
			array('LATITUDE, LONGITUDE', 'numerical'),
			array('USER_ID, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, ADR_SUBJECTRF, DATE_SENT_PROSECUTOR', 'length', 'max'=>10),
			array('ADR_CITY', 'length', 'max'=>50),
			array('STR_SUBJECTRF, username, description_locality, description_size', 'length'),
			array('COMMENT1, COMMENT2, COMMENT_GIBDD_REPLY, deletepict, upploadedPictures, request_gibdd, showUserHoles', 'safe'),	
			array('upploadedPictures', 'file', 'types'=>'jpg, jpeg, png, gif','maxFiles'=>10, 'allowEmpty'=>true, 'on' => 'update, import, fix'),
			array('upploadedPictures', 'file', 'types'=>'jpg, jpeg, png, gif','maxFiles'=>10, 'allowEmpty'=>false, 'on' => 'insert'),
			array('upploadedPictures', 'required', 'on' => 'insert', 'message' => 'Необходимо загрузить фотографии'),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, USER_ID, LATITUDE, LONGITUDE, ADDRESS, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, COMMENT1, COMMENT2, TYPE_ID, ADR_SUBJECTRF, ADR_CITY, COMMENT_GIBDD_REPLY, GIBDD_REPLY_RECEIVED, PREMODERATED, DATE_SENT_PROSECUTOR', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'subject'=>array(self::BELONGS_TO, 'RfSubjects', 'ADR_SUBJECTRF'),
			'requests'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id'),
			'pictures'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'order'=>'pictures.type, pictures.ordering'),
			'pictures_fresh'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'pictures_fresh.type="fresh"','order'=>'pictures_fresh.ordering'),
			'pictures_fixed'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'pictures_fixed.type="fixed"','order'=>'pictures_fixed.ordering'),
			'user_pictures_fixed'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'user_pictures_fixed.type="fixed" AND user_pictures_fixed.user_id='.Yii::app()->user->id,'order'=>'user_pictures_fixed.ordering'),
			'request_gibdd'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_gibdd.type="gibdd" AND request_gibdd.user_id='.Yii::app()->user->id),
			'request_prosecutor'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_prosecutor.type="prosecutor" AND user_id='.Yii::app()->user->id),
			'requests_gibdd'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'condition'=>'requests_gibdd.type="gibdd"','order'=>'requests_gibdd.date_sent ASC'),
			'requests_prosecutor'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'condition'=>'requests_prosecutor.type="prosecutor"','order'=>'date_sent ASC'),
			'fixeds'=>array(self::HAS_MANY, 'HoleFixeds', 'hole_id','order'=>'fixeds.date_fix ASC'),
			'user_fix'=>array(self::HAS_ONE, 'HoleFixeds', 'hole_id', 'condition'=>'user_fix.user_id='.Yii::app()->user->id),
			'type'=>array(self::BELONGS_TO, 'HoleTypes', 'TYPE_ID'),
			'user'=>array(self::BELONGS_TO, 'UserGroupsUser', 'USER_ID'),		
			'gibdd'=>array(self::BELONGS_TO, 'GibddHeads', 'gibdd_id'),
			'selected_lists'=>array(self::MANY_MANY, 'UserSelectedLists',
               '{{user_selected_lists_holes_xref}}(hole_id,list_id)'),
		);
	}
	
	public function behaviors(){
          return array( 'CAdvancedArBehavior' => array(
            'class' => 'application.extensions.CAdvancedArBehavior'));
    }

	public static function getAllstates()	
	{
	$arr=Array();
	$arr['fresh']      = 'Добавлен на сайт';
	$arr['inprogress'] = 'Заявление отправлено в ГИБДД';
	$arr['fixed']      = 'Исправлен';
	$arr['achtung']    = 'Просрочен';
	$arr['gibddre']    = 'Получен ответ из ГИБДД';
	$arr['prosecutor'] = 'Жалоба отправлена в прокуратуру';
	return $arr;
	}
	
	public static function getAllstatesShort()	
	{
	$arr=Array();
	$arr['fresh']      = 'Добавлено на сайт';
	$arr['inprogress'] = 'В ГАИ';
	$arr['fixed']      = 'Отремонтировано';
	$arr['achtung']    = 'В ГАИ';
	$arr['gibddre']    = 'Получен ответ';
	$arr['prosecutor'] = 'Заявление в прокуратуре';
	return $arr;
	}	
	
	public static function getAllstatesMany()	
	{
	$arr=Array();
	$arr['fresh']      = 'Новые';
	$arr['inprogress'] = 'Отправлено заявление';
	$arr['fixed']      = 'Сделаны';
	$arr['achtung']    = 'Не сделаны';
	$arr['gibddre']    = 'Получен ответ';
	$arr['prosecutor'] = 'Жалоба в прокуратуре';
	return $arr;
	}	
	
	public function getStateName()	
	{	
		return $this->AllstatesShort[$this->STATE];
	}
	
	public function getIsSelected()	
	{	
		foreach (Yii::app()->user->getState('selectedHoles', Array()) as $id) 
			if ($id==$this->ID) return true;
		return false;	
	}	
	
	public function getFixByUser($id)	
	{	
		foreach ($this->fixeds as $fix){
			if ($fix->user_id==$id) return $fix;
		}
		return null;
	}	
	
	const EARTH_RADIUS_KM = 6373;
	public function getTerritorialGibdd()	
	{	
		if (!$this->subject) return Array();
		$longitude=$this->LONGITUDE;
		$latitude=$this->LATITUDE;		
		$numerator = 'POW(COS(RADIANS(lat)) * SIN(ABS(RADIANS('.$longitude.')-RADIANS(lng))),2)';		
		$numerator .= ' + POW(
		COS(RADIANS('.$latitude.')) * SIN(RADIANS(lat)) - SIN(RADIANS('.$latitude.'))
		* COS(RADIANS(lat))*COS(ABS(RADIANS('.$longitude.')-RADIANS(lng)))
		,2)';
		$numerator = 'SQRT('.$numerator.')';		
		$denominator = 'SIN(RADIANS(lat))*SIN(RADIANS('.$latitude.')) +
		COS(RADIANS(lat))*COS(RADIANS('.$latitude.'))*
		COS(ABS(RADIANS('.$longitude.')-RADIANS(lng)))';		
		$condition = 'ATAN('.$numerator.'/('.$denominator.')) * '.self::EARTH_RADIUS_KM;
		
		$criteria=new CDbCriteria;
		$criteria->select=Array('*', $condition.' as distance');				
		$criteria->condition='lat > 0 AND lng > 0';	
		$criteria->addCondition('moderated = 1 OR author_id='.Yii::app()->user->id);
		if ($this->subject) $criteria->addCondition('subject_id='.$this->subject->id);
		$criteria->order='ABS(distance) ASC';		
		$criteria->having='ABS(distance) < 1000';
		$criteria->limit=5;
		$gibdds=GibddHeads::model()->findAll($criteria);
		if ($this->subject) array_unshift ($gibdds, $this->subject->gibdd);
		return $gibdds;
	}
		
	
	public function getUpploadedPictures(){
		return CUploadedFile::getInstancesByName('');
	}
	
	public function savePictures(){						
		foreach ($this->deletepict as $pictid) {
			$pictmodel=HolePictures::model()->findByPk((int)$pictid);  
			if ($pictmodel)$pictmodel->delete();
		}

		$imagess=$this->UpploadedPictures;
		$id=$this->ID;
		$prefix='';						
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id)){
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id))
			{
				$this->addError('upploadedPictures', Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$id))
			{
				unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id);
				$this->addError('upploadedPictures',Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/small/'.$id))
			{
				unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id);
				unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$id);
				$this->addError('upploadedPictures',Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
		}						

		$_params=$this->params;
		$file_counter = 0;
		$k = $this->ID;			
		$pictdir=$_SERVER['DOCUMENT_ROOT'].'/upload/st1234/';
						
        foreach ($imagess as $_file){
			if(!$_file->hasError)
			{	
				$imgname=rand().'.jpg';
				$image = $this->imagecreatefromfile($_file->getTempName(), &$_image_info);
				if(!$image)
				{
					$this->addError('pictures',Yii::t('errors', 'GREENSIGHT_ERROR_UNSUPPORTED_IMAGE_TYPE'));
					return false;
				}
				$aspect = max($_image_info[0] / $_params['big_sizex'], $_image_info[1] / $_params['big_sizey']);
				if($aspect > 1)
				{
					$new_x    = floor($_image_info[0] / $aspect);
					$new_y    = floor($_image_info[1] / $aspect);
					$newimage = imagecreatetruecolor($new_x, $new_y);
					imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_x, $new_y, $_image_info[0], $_image_info[1]);
					imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id.'/'.$imgname);
				}
				else
				{
					imagejpeg($image, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id.'/'.$imgname);
				}
	
				$aspect   = max($_image_info[0] / $_params['medium_sizex'], $_image_info[1] / $_params['medium_sizey']);
				$new_x    = floor($_image_info[0] / $aspect);
				$new_y    = floor($_image_info[1] / $aspect);
				$newimage = imagecreatetruecolor($new_x, $new_y);
				imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_x, $new_y, $_image_info[0], $_image_info[1]);
				imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$id.'/'.$imgname);
				imagedestroy($newimage);
				$aspect   = min($_image_info[0] / $_params['small_sizex'], $_image_info[1] / $_params['small_sizey']);
				$newimage = imagecreatetruecolor($_params['small_sizex'], $_params['small_sizey']);
				imagecopyresampled
				(
					$newimage,
					$image,
					0,
					0,
					$_image_info[0] > $_image_info[1] ? floor(($_image_info[0] - $aspect * $_params['small_sizex']) / 2) : 0,
					$_image_info[0] < $_image_info[1] ? floor(($_image_info[1] - $aspect * $_params['small_sizey']) / 2) : 0,
					$_params['small_sizex'],
					$_params['small_sizey'],
					ceil($aspect * $_params['small_sizex']),
					ceil($aspect * $_params['small_sizey'])
				);
				imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/small/'.$id.'/'.$imgname);
				imagedestroy($newimage);
				imagedestroy($image);
							
				$imgmodel=new HolePictures;
				$imgmodel->type=$this->scenario=='fix'?'fixed':'fresh'; 
				$imgmodel->filename=$imgname;
				$imgmodel->hole_id=$this->ID;
				$imgmodel->user_id=Yii::app()->user->id;
				$imgmodel->ordering=$imgmodel->lastOrder+1;
				$imgmodel->save();
			}
		}
		return true;			
	}

	public static function imagecreatefromfile($file_name, &$_image_info = array())
	{
		$_image_info = getimagesize($file_name, &$_image_additional_info);
		$_image_info['additional'] = $_image_additional_info;
		switch($_image_info['mime'])
		{
			case 'image/jpeg':
			case 'image/pjpg':
			{
				$operator = 'imagecreatefromjpeg';
				break;
			}
			case 'image/gif':
			{
				$operator = 'imagecreatefromgif';
				break;
			}
			case 'image/png':
			case 'image/x-png':
			{
				$operator = 'imagecreatefrompng';
				break;
			}
			default:
			{
				return false;
			}
		}
		return $operator($file_name);
	}	
	
	
	public function updateToprosecutor(){
	
	if ($this->STATE!='achtung') return false;
	$this->DATE_STATUS= time();
	$this->DATE_SENT_PROSECUTOR = time();
	$this->STATE='prosecutor';
	$this->archive=0;
	$this->update();
	return true;
	}
	
	public function updateRevokep(){
	
	if ($this->request_prosecutor) {
		$this->request_prosecutor->delete();	
		if (!count(HoleRequests::model()->findAll('hole_id='.$this->ID.' AND type="prosecutor"'))) {
						$this->DATE_STATUS= time();
						$this->DATE_SENT_PROSECUTOR = null;
						$this->STATE='achtung';
						$this->archive=0;
						$this->update();
					}
		return true;			
		}
	else return false;	

	}	
	
	public function makeRequest($type){
		$attr='request_'.$type;
		if (!$this->$attr){
			$request=new HoleRequests;
			$request->attributes=Array(
							'hole_id'=>$this->ID,
							'user_id'=>Yii::app()->user->id,
							//'gibdd_id'=>$this->subject ? $this->subject->gibdd->id : 0,
							'gibdd_id'=>$this->gibdd_id,
							'date_sent'=>time(),
							'type'=>$type,
							);
			if ($request->save()){
			if ($type=='gibdd') if ($this->updateSetinprogress()) return true;
			elseif ($type=='prosecutor') if ($this->updateToprosecutor()) return true;
			}
		}
		elseif ($type=='prosecutor' && $this->STATE=='achtung') $this->updateToprosecutor();
		return true;
	}

	
	public function updateSetinprogress()
	{
		if($this->STATE != 'fresh' && !($this->STATE == 'fixed' && !sizeof($this->user_pictures_fixed)))
				{
					return false;
				}
		else {			
			if ($this->user_fix) $this->user_fix->delete();	
			$this->DATE_STATUS=time();
			if (count ($this->fixeds) == 0) {					
					if($this->STATE == 'fresh')  
					{
						if (!$this->DATE_SENT) {
							$this->DATE_SENT = time(); 						
						}
						$this->STATE='inprogress';										
					}
					else
					{
						if($this->DATE_SENT)
						{
							$this->STATE = 'inprogress';
						}						
						if($this->DATE_SENT < time() - 37 * 86400)
						{
							$this->STATE = 'achtung';
						}
						if($this->GIBDD_REPLY_RECEIVED)
						{
							$this->STATE = 'gibddre';
						}
						if($this->DATE_SENT_PROSECUTOR)
						{
							$this->STATE = 'prosecutor';
						}
						if(!$this->DATE_SENT)
						{
							$this->STATE = 'fresh';
							if ($this->request_gibdd) $this->request_gibdd->delete();
						}
					}
				}
			$this->archive=0;	
			if ($this->update()) return true;
			else return false;
		}	
	}
	
	public function updateRevoke()
	{
			if(!$this->request_gibdd || $this->request_gibdd->answer)
				{
					return false;	
				}
				$this->DATE_STATUS = time();
				$this->request_gibdd->delete();
				if (!count(HoleRequests::model()->findAll('hole_id='.$this->ID.' AND type="gibdd"'))) {
					$this->DATE_SENT = null;
					$this->DATE_STATUS = time();
					$this->STATE = 'fresh';
					}
				else {
					$this->DATE_SENT = $this->requests_gibdd[0]->date_sent;
				}	
			$this->archive=0;	
			if ($this->update()) return true;
			else return false;
	}		
	
	
	public function afterFind(){
	
		//if (!$this->pictures || !$this->user) $this->delete();
		//вычисляем количество дней с момента отправки
		if(($this->STATE == 'inprogress' || $this->STATE == 'achtung') && $this->DATE_SENT && !$this->STATE != 'gibddre')
		{
			$this->WAIT_DAYS = 38 - ceil((time() - $this->DATE_SENT) / 86400);	
		}
			
		//отмечаем яму если просроченна
		if ($this->WAIT_DAYS < 0 && $this->STATE == 'inprogress') {
			$this->STATE = 'achtung';
			$this->update();
		}
		elseif ($this->STATE == 'achtung' && $this->WAIT_DAYS > 0){
			$this->STATE = 'inprogress';
			$this->update();			
		}
		
		if ($this->WAIT_DAYS<0) { 
			$this->PAST_DAYS=abs($this->WAIT_DAYS);
			$this->WAIT_DAYS=0;
		}		
	}
	
	public function BeforeDelete(){
				//сначала удаляем все картинки
				foreach ($this->pictures as $picture) $picture->delete();			
				
				//Потом удаляем все запросы вместе со всем содержимым
				foreach ($this->requests as $request) $request->delete();
				
				//Потом все отметки об исправленности
				foreach ($this->fixeds as $fixed) $fixed->delete();
				
				$this->selected_lists=Array();
				$this->update();
	
				return true;
	}	
	
	public function BeforeSave(){
				parent::beforeSave();
				$this->DATE_STATUS = time();
				$this->ADR_CITY=trim($this->ADR_CITY);
					if ($this->scenario=='fix'){
						$fixmodel=new HoleFixeds;
						$fixmodel->user_id=Yii::app()->user->id;
						$fixmodel->hole_id=$this->ID;
						$fixmodel->date_fix=time();
						$fixmodel->comment=$this->COMMENT2;
						$fixmodel->save();					
					}
				return true;
	}		
	
	public function getIsUserHole(){				
				if ($this->USER_ID==Yii::app()->user->id) return true;
				else return false;
	}	
	
	public function getmodering(){
		if ($this->PREMODERATED) {$publtext='снять модерацию'; $pubimg='published.png';}
		else {$publtext='отмодерировать';  $pubimg='unpublished.png';}
		return '<a class="publish ajaxupdate" title="'.$publtext.'" href="'.Yii::app()->getController()->CreateUrl("moderate", Array('id'=>$this->ID)).'">
			<img src="/images/'.$pubimg.'" alt="'.$publtext.'"/>
			</a>';
	}	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'USER_ID' => 'Пользователь',
			'LATITUDE' => 'Latitude',
			'LONGITUDE' => 'Longitude',
			'ADDRESS' => 'Адрес дефекта',
			'gibdd_id'=>'Отдел ГИБДД',
			'STATE' => 'Статус',
			'DATE_CREATED' => 'Дата создания',
			'DATE_SENT' => 'Дата отправки в ГИБДД',
			'DATE_STATUS' => 'Дата изменения',
			'COMMENT1' => 'Комментарии',
			'COMMENT2' => 'Комментарии',
			'TYPE_ID' => 'Тип дефекта',
			'ADR_SUBJECTRF' => 'Субъект РФ',
			'ADR_CITY' => 'Город',
			'COMMENT_GIBDD_REPLY' => 'Comment Gibdd Reply',
			'GIBDD_REPLY_RECEIVED' => 'Gibdd Reply Received',
			'PREMODERATED' => 'Модер.',
			'NOT_PREMODERATED' => 'только непроверенные',
			'DATE_SENT_PROSECUTOR' => 'Date Sent Prosecutor',
			'deletepict'=>'Удалить фотографию?',
			'replуfiles'=>'Необходимо добавить отсканированный ответ из ГИБДД',
			'upploadedPictures'=>$this->scenario=='fix' ? 'Желательно добавить фотографии исправленного дефекта' : 'Нужно загрузить фотографии (не больше 10 штук)',
			'description_size'=>'Описание дефекта (размеры и прочая информация)',
			'description_locality'=>'Подробное описание расположения дефекта на местности',
			'archive'=>'Архив',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function getisEmptyAttribs()	
	{
		$ret=true;
		foreach ($this->attributes as $attr){
			if($attr) $ret=false;
		}
		return $ret;

	}
	
	public function userSearch()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$user=Yii::app()->user;
		$userid=$user->id;
		$criteria=new CDbCriteria;
		//$criteria->with=Array('pictures_fresh','pictures_fixed');
		$criteria->with=Array('type','pictures_fresh');
		$criteria->compare('t.ID',$this->ID,false);
		if (!$this->showUserHoles || $this->showUserHoles==1) $criteria->compare('t.USER_ID',$userid,false);
		elseif ($this->showUserHoles==2) {
			$criteria->with=Array('type','pictures_fresh','requests');
			$criteria->addCondition('t.USER_ID!='.$userid);
			$criteria->compare('requests.user_id',$userid,true);
			$criteria->together=true;
			}
			
		//Вытаскиваем все Айдишники для селектора фильтра по ГИБДД	
		if (!$this->selecledList){
			$tmpcriteria=clone $criteria;
			$tmpcriteria->select='ID';
			$this->keys=CHtml::listData($this->findAll($tmpcriteria),'ID','ID');	
		}
		
		$criteria->compare('t.STATE',$this->STATE,true);	
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('t.gibdd_id',$this->gibdd_id,false);
		$criteria->compare('type.alias',$this->type_alias,true);	
		
		if (!$user->userModel->relProfile->show_archive_holes) $criteria->compare('t.archive',0,false);
		
		if ($this->selecledList)
			$criteria->join='INNER JOIN {{user_selected_lists_holes_xref}} ON {{user_selected_lists_holes_xref}}.hole_id=t.id AND {{user_selected_lists_holes_xref}}.list_id='.$this->selecledList;
		
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=>$this->limit ? $this->limit : 12,				        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));
	}	
	
	public function areaSearch($user)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		$area=$user->userModel->hole_area;
		
		$userid=$user->id;
		
		$criteria=new CDbCriteria;
		$criteria->with=Array('type','pictures_fresh');		
		
		foreach ($area as $shape){
			$criteria->addCondition('LATITUDE >= '.$shape->points[0]->lat
			.' AND LATITUDE <= '.$shape->points[2]->lat
			.' AND LONGITUDE >= '.$shape->points[0]->lng
			.' AND LONGITUDE <= '.$shape->points[2]->lng, 'OR');
			}	
		
		//Вытаскиваем все Айдишники для селектора фильтра по ГИБДД	
		$tmpcriteria=clone $criteria;
		$tmpcriteria->with=Array();
		$tmpcriteria->select='ID';
		$this->keys=CHtml::listData($this->findAll($tmpcriteria),'ID','ID');		
		
		if ($this->showUserHoles==1) $criteria->compare('t.USER_ID',$userid,false);
		elseif ($this->showUserHoles==2) {
			$criteria->with=Array('type','pictures_fresh','requests');
			$criteria->addCondition('t.USER_ID!='.$userid);
			$criteria->compare('requests.user_id',$userid,true);
			$criteria->together=true;
			}			
			
		$criteria->compare('t.ID',$this->ID,false);			
			
		if (!$user->userModel->relProfile->show_archive_holes) $criteria->compare('t.archive',0,false);
		
		$criteria->compare('t.STATE',$this->STATE,true);	
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);
		$criteria->compare('t.gibdd_id',$this->gibdd_id,false);
		//
		//$criteria->addCondition('t.USER_ID='.$userid);
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=>$this->limit ? $this->limit : 12,				        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));
	}	
	
	
	
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		//$criteria->with=Array('pictures_fresh','pictures_fixed');
		$criteria->with=Array('type','pictures_fresh');
		$criteria->compare('t.ID',$this->ID,false);
		$criteria->compare('t.USER_ID',$this->USER_ID,false);
		$criteria->compare('t.LATITUDE',$this->LATITUDE);
		$criteria->compare('t.LONGITUDE',$this->LONGITUDE);
		$criteria->compare('t.ADDRESS',$this->ADDRESS,true);
		$criteria->compare('t.STATE',$this->STATE,true);
		$criteria->compare('t.DATE_CREATED',$this->DATE_CREATED,true);
		$criteria->compare('t.DATE_SENT',$this->DATE_SENT,true);
		$criteria->compare('t.DATE_STATUS',$this->DATE_STATUS,true);
		$criteria->compare('t.COMMENT1',$this->COMMENT1,true);
		$criteria->compare('t.COMMENT2',$this->COMMENT2,true);
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);
		$criteria->compare('t.ADR_SUBJECTRF',$this->ADR_SUBJECTRF,false);
		$criteria->compare('t.ADR_CITY',$this->ADR_CITY,true);
		$criteria->compare('t.COMMENT_GIBDD_REPLY',$this->COMMENT_GIBDD_REPLY,true);
		$criteria->compare('t.GIBDD_REPLY_RECEIVED',$this->GIBDD_REPLY_RECEIVED);
		if ($this->NOT_PREMODERATED) $criteria->compare('PREMODERATED',0);
		$criteria->compare('archive',$this->archive ? $this->archive : 0);
		if (!Yii::app()->user->isModer) $criteria->compare('PREMODERATED',$this->PREMODERATED,true);
		$criteria->compare('DATE_SENT_PROSECUTOR',$this->DATE_SENT_PROSECUTOR,true);
		//$criteria->together=true;
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=>$this->limit ? $this->limit : 12,				        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));
	}
	
	public function searchInAdmin()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		//$criteria->with=Array('pictures_fresh','pictures_fixed');
		$criteria->with=Array('type','user','subject', 'gibdd');
		$criteria->compare('t.ID',$this->ID,false);
		$criteria->compare('user.username',$this->username,true);
		$criteria->compare('t.LATITUDE',$this->LATITUDE);
		$criteria->compare('t.LONGITUDE',$this->LONGITUDE);
		$criteria->compare('t.ADDRESS',$this->ADDRESS,true);
		$criteria->compare('t.STATE',$this->STATE,true);
		if ($this->DATE_CREATED) {
			$DATE_CREATED=CDateTimeParser::parse($this->DATE_CREATED, 'dd.MM.yyyy');
			$criteria->addCondition('t.DATE_CREATED >='.$DATE_CREATED.' AND t.DATE_CREATED <='.($DATE_CREATED+86400));
			}		
		$criteria->compare('t.DATE_SENT',$this->DATE_SENT,true);
		if ($this->DATE_STATUS) {
			$DATE_STATUS=CDateTimeParser::parse($this->DATE_STATUS, 'dd.MM.yyyy');
			$criteria->addCondition('t.DATE_STATUS <='.$DATE_STATUS);
			
			}
		$criteria->compare('t.COMMENT1',$this->COMMENT1,true);
		$criteria->compare('t.COMMENT2',$this->COMMENT2,true);
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);
		$criteria->compare('subject.name_full',$this->ADR_SUBJECTRF,true);
		$criteria->compare('gibdd.name',$this->gibdd_id,true);
		$criteria->compare('t.ADR_CITY',$this->ADR_CITY,true);
		$criteria->compare('t.COMMENT_GIBDD_REPLY',$this->COMMENT_GIBDD_REPLY,true);
		$criteria->compare('t.GIBDD_REPLY_RECEIVED',$this->GIBDD_REPLY_RECEIVED);
		$criteria->compare('t.PREMODERATED',$this->PREMODERATED,true);
		$criteria->compare('t.archive',$this->archive,true);
		$criteria->compare('t.DATE_SENT_PROSECUTOR',$this->DATE_SENT_PROSECUTOR,true);
		$criteria->together=true;
	
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=> Yii::app()->user->getState('pageSize',20),			        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));
	}		
	
	public function getArchiveSearchLink()
	{
		$arr=Array('/holes/index');
		foreach($this->attributes as $key=>$val)
			$arr["Holes[$key]"]=$val;
		$arr["Holes[archive]"]=1;   
		return $arr;	
	}
}
