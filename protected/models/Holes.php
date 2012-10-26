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
	public $polygonIds;
	public $keys=Array();
	public $polygons=Array();
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
			array('GIBDD_REPLY_RECEIVED, PREMODERATED, TYPE_ID, NOT_PREMODERATED, archive, deleted, premoderator_id, deletor_id', 'numerical', 'integerOnly'=>true),
			array('LATITUDE, LONGITUDE', 'numerical'),
			array('USER_ID, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, ADR_SUBJECTRF, DATE_SENT_PROSECUTOR', 'length', 'max'=>10),
			array('ADR_CITY', 'length', 'max'=>50),
			array('STR_SUBJECTRF, username, description_locality, description_size', 'length'),
			array('COMMENT1, COMMENT2, COMMENT_GIBDD_REPLY, deletepict, upploadedPictures, request_gibdd, showUserHoles', 'safe'),	
			array('upploadedPictures', 'file', 'types'=>'jpg, jpeg, png, gif','maxFiles'=>10, 'allowEmpty'=>true, 'on' => 'update, import, fix'),
			array('upploadedPictures', 'file', 'types'=>'jpg, jpeg, png, gif','maxFiles'=>10, 'allowEmpty'=>false, 'on' => 'insert'),
			array('upploadedPictures', 'unsafe', 'on' => 'add'),
			array('upploadedPictures', 'required', 'on' => 'insert, add', 'message' => 'Необходимо загрузить фотографии'),			
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
			'requests_with_answers'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'with'=>'answers', 'condition'=>'answers.id > 0', 'order'=>'requests_with_answers.date_sent, answers.date'),
			'pictures'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'order'=>'pictures.type, pictures.ordering AND pictures.premoderated=1'),
			'pictures_fresh'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'pictures_fresh.type="fresh" AND pictures_fresh.premoderated=1','order'=>'pictures_fresh.ordering'),
			'pictures_fixed'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'pictures_fixed.type="fixed" AND pictures_fixed.premoderated=1','order'=>'pictures_fixed.ordering'),
			'user_pictures_fixed'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'user_pictures_fixed.type="fixed" AND user_pictures_fixed.user_id='.Yii::app()->user->id,'order'=>'user_pictures_fixed.ordering'),
			'pictures_fixed_not_moderated'=>array(self::HAS_MANY, 'HolePictures', 'hole_id', 'condition'=>'pictures_fixed_not_moderated.type="fixed" AND pictures_fixed_not_moderated.premoderated=0','order'=>'pictures_fixed_not_moderated.ordering'),
			'request_gibdd'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_gibdd.type="gibdd" AND request_gibdd.user_id='.Yii::app()->user->id),
			'request_prosecutor'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_prosecutor.type="prosecutor" AND user_id='.Yii::app()->user->id),
			'requests_gibdd'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'condition'=>'requests_gibdd.type="gibdd"','order'=>'requests_gibdd.date_sent ASC'),
			'requests_prosecutor'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'condition'=>'requests_prosecutor.type="prosecutor"','order'=>'date_sent ASC'),
			'requests_with_answer_comment'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'with'=>'answers','condition'=>'answers.comment !=""','order'=>'requests_with_answer_comment.date_sent DESC'),
			'requests_answers'=>array(self::HAS_MANY, 'HoleRequests', 'hole_id', 'condition'=>'requests_gibdd.type="gibdd"','order'=>'requests_gibdd.date_sent DESC'),
			'fixeds'=>array(self::HAS_MANY, 'HoleFixeds', 'hole_id','order'=>'fixeds.date_fix ASC'),
			'user_fix'=>array(self::HAS_ONE, 'HoleFixeds', 'hole_id', 'condition'=>'user_fix.user_id='.Yii::app()->user->id),
			'type'=>array(self::BELONGS_TO, 'HoleTypes', 'TYPE_ID'),
			'user'=>array(self::BELONGS_TO, 'UserGroupsUser', 'USER_ID'),		
			'moder'=>array(self::BELONGS_TO, 'UserGroupsUser', 'premoderator_id'),
			'deletor'=>array(self::BELONGS_TO, 'UserGroupsUser', 'deletor_id'),
			'gibdd'=>array(self::BELONGS_TO, 'GibddHeads', 'gibdd_id'),
			'selected_lists'=>array(self::MANY_MANY, 'UserSelectedLists',
               '{{user_selected_lists_holes_xref}}(hole_id,list_id)'),
            'comments_cnt'=> array(self::STAT, 'Comment', 'owner_id', 'condition'=>'owner_name="Holes" AND status < 2'),   
            'comments'=> array(self::HAS_MANY, 'Comment', 'owner_id', 'condition'=>'owner_name="Holes"'), 
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
	
	public function getAllAnswers()	
	{
		return HoleAnswers::model()->with('request')->findAll(Array('condition'=>'request.hole_id='.$this->ID.' AND request.id=t.request_id', 'order'=>'t.date ASC'));
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
	
	public function getIsMoscow()	
	{	
		if ($this->STATE != 'fixed' && $this->type->dorogimos_id && $this->subject && $this->subject->region_num==77) return true;
		else return false;
	}	
	
	public function getFixByUser($id)	
	{	
		foreach ($this->fixeds as $fix){
			if ($fix->user_id==$id) return $fix;
		}
		return null;
	}	
	
	public function getBigFolder()	
	{	
		return date('m_Y', $this->DATE_CREATED);
	} 
	
	const EARTH_RADIUS_KM = 6373;
	public function getTerritorialGibdd()	
	{	
		//if (!$this->subject) return Array();
		$longitude=$this->LONGITUDE;
		$latitude=$this->LATITUDE;		
		/* $numerator = 'POW(COS(RADIANS(lat)) * SIN(ABS(RADIANS('.$longitude.')-RADIANS(lng))),2)';		
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
		if ($this->subject) array_unshift ($gibdds, $this->subject->gibdd);*/
		
		
		$gibdds=GibddHeads::model()->with('areas')->findAll(Array('order'=>'t.level DESC, t.subject_id DESC'));
		
		$regionalGibdds=Array();
		$regkey=0;
		foreach ($gibdds as $i=>$gibdd){
			$inpolyg=false;
			foreach ($gibdd->areas as $area){
				$inpolyg=$this->inPolygon($area, $this, 'points');
				if ($inpolyg) break;
			}	
			if (!$inpolyg) unset ($gibdds[$i]);
			else if ($gibdd->level==1) {$regionalGibdds[$i]=$gibdd; $regkey=$i;}
		}
		
		if ($regionalGibdds){			
			if (count($regionalGibdds) > 1){				
				foreach ($regionalGibdds as $i=>$gibdd){
					$mindist=999999999*9999999;
					foreach ($gibdd->areas as $area){
						foreach ($area->points as $point){
						$dist=sqrt(pow($point->lat - $this->LATITUDE, 2) + pow($point->lng - $this->LONGITUDE, 2));
						if ($dist < $mindist) $mindist=$dist;
						}
					}
					$regionalGibdds[$i]->mindist=$mindist;
				}
				
				$mindist=$regionalGibdds[$regkey]->mindist;
				
				foreach ($regionalGibdds as $i=>$gibdd){						
						if ($gibdd->mindist <= $mindist) $mindist=$gibdd->mindist;
						else unset($gibdds[$i]);
					}
			}	
		}	
		
		$newArr=Array();		
		foreach ($gibdds as $gibdd) $newArr[]=$gibdd;		
		return $newArr;
	}
		
	
	public function getUpploadedPictures(){
		$session=new CHttpSession;
		$session->open();
		
		$folder=$_SERVER['DOCUMENT_ROOT'].'/upload/tmp/'.$session->SessionID;
		if (is_dir($folder)) $files=CFileHelper::findFiles($folder,Array('level'=>0));		
		else $files=Array();
		
		if (!$files)
			return CUploadedFile::getInstancesByName('');
		else 
			return $files;	
	}
	
	public function savePictures(){						
		foreach ($this->deletepict as $pictid) {
			$pictmodel=HolePictures::model()->findByPk((int)$pictid);  
			if ($pictmodel)$pictmodel->delete();
		}
		$imagess=$this->UpploadedPictures;
		//print_r($imagess); die();
		$id=$this->ID;
		$prefix='';	
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload')) mkdir($_SERVER['DOCUMENT_ROOT'].'/upload');
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234')) mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234');
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder)) mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder);
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original')) mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original');
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/medium')) mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/medium');
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/small')) mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/small');
		
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original/'.$id)){
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original/'.$id))
			{
				$this->addError('upploadedPictures', Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/medium/'.$id))
			{
				unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original/'.$id);
				$this->addError('upploadedPictures',Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/small/'.$id))
			{
				unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original/'.$id);
				unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/medium/'.$id);
				$this->addError('upploadedPictures',Yii::t('errors', 'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'));
				return false;
			}
		}						

		$_params=$this->params;
		$file_counter = 0;
		$k = $this->ID;			
		$pictdir=$_SERVER['DOCUMENT_ROOT'].'/upload/st1234/';
						
        foreach ($imagess as $_file){
			if(is_string($_file) || !$_file->hasError)
			{	
				$imgname=rand().'.jpg';
				$tempname=is_string($_file) ? $_file : $_file->getTempName();
				$image = $this->imagecreatefromfile($tempname, &$_image_info);
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
					imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original/'.$id.'/'.$imgname);
				}
				else
				{
					imagejpeg($image, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/original/'.$id.'/'.$imgname);
				}
	
				$aspect   = max($_image_info[0] / $_params['medium_sizex'], $_image_info[1] / $_params['medium_sizey']);
				$new_x    = floor($_image_info[0] / $aspect);
				$new_y    = floor($_image_info[1] / $aspect);
				$newimage = imagecreatetruecolor($new_x, $new_y);
				imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_x, $new_y, $_image_info[0], $_image_info[1]);
				imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/medium/'.$id.'/'.$imgname);
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
				imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/'.$this->bigFolder.'/small/'.$id.'/'.$imgname);
				imagedestroy($newimage);
				imagedestroy($image);
							
				$imgmodel=new HolePictures;
				$imgmodel->type=$this->scenario=='fix' || $this->scenario=='addFixedFiles' ?'fixed':'fresh'; 
				$imgmodel->filename=$imgname;
				$imgmodel->hole_id=$this->ID;
				$imgmodel->user_id=Yii::app()->user->id;
				$imgmodel->ordering=$imgmodel->lastOrder+1;
				if ($this->scenario=='addFixedFiles') $imgmodel->premoderated= $imgmodel->user_id != $this->USER_ID ? 0 : 1;
				$imgmodel->save();
			}
		}
		Yii::app()->controller->flushUploadDir();
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
				
				//Потом все комментарии к яме
				foreach ($this->comments as $comment) $comment->delete();
				
				$this->selected_lists=Array();
				$this->update();
	
				return true;
	}	
	
	public function BeforeSave(){
				parent::beforeSave();
				$this->DATE_STATUS = time();
				
				$Subs = array(
						'Город' => '',
						'город' => '',
						);
				$this->ADR_CITY = trim(strtr($this->ADR_CITY,$Subs));		

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
	
	public function newCommentInHole($comment){				
		if($this->user->email && $this->user->id!=$comment->user->id){
			$headers = "MIME-Version: 1.0\r\nFrom: \"Rosyama\" <".Yii::app()->params['adminEmail'].">\r\nReply-To: ".Yii::app()->params['adminEmail']."\r\nContent-Type: text/html; charset=utf-8";
			Yii::app()->request->baseUrl='http://'.$_SERVER['HTTP_HOST'];
			$mailbody=Yii::app()->controller->renderPartial('//ugmail/newComment', Array(
						'hole'=>$this,
						'comment'=>$comment,
						'user'=>$this->user,
						),true);
			if (mail($this->user->email,"=?utf-8?B?" . base64_encode('Новый комментарий к Вашей яме') . "?=",$mailbody,$headers)){
							return true;
						}		
			}	
			return false;
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
			'LATITUDE' => 'Широта',
			'LONGITUDE' => 'Долгота',
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
			'deleted'=>'Удалено'
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
		$criteria->with=Array('type','pictures_fresh', 'comments_cnt');
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
		$criteria->compare('t.deleted',0);
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
	
	public function inPolygon($polygon, $point, $relname='points'){
			$i=0;
			$j=count ($polygon->$relname)-1;			
			$c = 0;
			$points=$polygon->$relname;
			for ($i=0; $i<count($polygon->$relname); $j=$i++){
				if (((($points[$i]->lat <= $point->LATITUDE) && ($point->LATITUDE < $points[$j]->lat)) || (($points[$j]->lat <= $point->LATITUDE) && ($point->LATITUDE < $points[$i]->lat))) && 
						 ($point->LONGITUDE > ($points[$j]->lng - $points[$i]->lng) * ($point->LATITUDE - $points[$i]->lat) / ($points[$j]->lat - $points[$i]->lat) + $points[$i]->lng)
						 ) {
						 $c = !$c;
					 }	
					 
				}
				
		return $c;
	}
	
	public function inPolygonArray($polygon, $point){
			$i=0;
			$j=count ($polygon)-1;			
			$c = 0;
			$points=$polygon;
			for ($i=0; $i<count($polygon); $j=$i++){
				if (((($points[$i]['lat'] <= $point->LATITUDE) && ($point->LATITUDE < $points[$j]['lat'])) || (($points[$j]['lat'] <= $point->LATITUDE) && ($point->LATITUDE < $points[$i]['lat']))) && 
						 ($point->LONGITUDE > ($points[$j]['lng'] - $points[$i]['lng']) * ($point->LATITUDE - $points[$i]['lat']) / ($points[$j]['lat'] - $points[$i]['lat']) + $points[$i]['lng'])
						 ) {
						 $c = !$c;
					 }	
					 
				}
				
		return $c;
	}	
	
	public function findPkeysInAreaByUser($userModel)
	{
	
		$area=$userModel->hole_area;		

		//Вытаскиваем айдишники ям в полигонах		
		$polygonHolesIds=Array();
		foreach ($area as $shape){
			$polygonCriteria=new CDbCriteria;
			$cond='LONGITUDE >= '.$shape->corners['left']
			.' AND LONGITUDE <= '.$shape->corners['right']
			.' AND LATITUDE >= '.$shape->corners['bottom']
			.' AND LATITUDE <= '.$shape->corners['top'];		
			
			$polygonCriteria->addCondition($cond);					
			
			$polygonCriteria->select='ID, LATITUDE, LONGITUDE';
			$polygonHoles=$this->findAll($polygonCriteria);
			foreach ($polygonHoles as $item)
					if ($this->inPolygon($shape, $item)) $polygonHolesIds[]=$item->ID;			
			}	
		
		return $polygonHolesIds;
	
	}
	
	public function findPkeysNotInAreaByUser($userModel)
	{
	
		$area=$userModel->hole_area;		
		
		//Вытаскиваем айдишники ям не в полигонах		
		$polygonHolesIds=Array();
		foreach ($area as $i=>$shape){
			$polygonCriteria=new CDbCriteria;
			$cond='LONGITUDE >= '.$shape->corners['left']
			.' AND LONGITUDE <= '.$shape->corners['right']
			.' AND LATITUDE >= '.$shape->corners['bottom']
			.' AND LATITUDE <= '.$shape->corners['top'];		
			
			$polygonCriteria->addCondition($cond);					
			
			$polygonCriteria->select='ID, LATITUDE, LONGITUDE';
			$polygonHoles=$this->findAll($polygonCriteria);
			foreach ($polygonHoles as $item){
					$inPolygon=$this->inPolygon($shape, $item);
					if (!$inPolygon) $polygonHolesIds[$item->ID]=$item->ID;
					else unset ($polygonHolesIds[$item->ID]);
						
			}	
		}			
		return $polygonHolesIds;
	
	}
	
	public function findPkeysNotInArea($polygons, $corners)
	{
	
		//Вытаскиваем айдишники ям не в полигонах		
		$polygonHolesIds=Array();
		foreach ($polygons as $i=>$polygon){
			$polygonCriteria=new CDbCriteria;
			$cond='LONGITUDE >= '.$corners[$i]['left']
			.' AND LONGITUDE <= '.$corners[$i]['right']
			.' AND LATITUDE >= '.$corners[$i]['bottom']
			.' AND LATITUDE <= '.$corners[$i]['top'];		
			
			$polygonCriteria->addCondition($cond);					
			
			$polygonCriteria->select='ID, LATITUDE, LONGITUDE';
			$polygonHoles=$this->findAll($polygonCriteria);
			foreach ($polygonHoles as $item){
					$inPolygon=$this->inPolygonArray($polygon, $item);
					if (!$inPolygon) $polygonHolesIds[$item->ID]=$item->ID;
					else unset ($polygonHolesIds[$item->ID]);
						
			}	
		}			
		return $polygonHolesIds;
	
	}	
	
	
	public function areaSearch($user)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.		

		$userid=$user->id;		
		
		$criteria=new CDbCriteria;
		$criteria->with=Array('type','pictures_fresh', 'comments_cnt');		
		
		$area=$user->userModel->hole_area;
		
		foreach ($area as $shape){
			$cond='LONGITUDE >= '.$shape->corners['left']
			.' AND LONGITUDE <= '.$shape->corners['right']
			.' AND LATITUDE >= '.$shape->corners['bottom']
			.' AND LATITUDE <= '.$shape->corners['top'];					
			$criteria->addCondition($cond,'OR');			
			}
		
		
		$notPolygonHolesIds=$this->findPkeysNotInAreaByUser($user->userModel);
		if ($notPolygonHolesIds) $criteria->addNotInCondition('t.ID',$notPolygonHolesIds);	
		
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
		
			
		if (!$user->userModel->relProfile->show_archive_holes) $criteria->compare('t.archive',0,false);
		
		$criteria->compare('t.deleted',0);
		$criteria->compare('t.STATE',$this->STATE,true);	
		$criteria->compare('t.TYPE_ID',$this->TYPE_ID,false);
		$criteria->compare('type.alias',$this->type_alias,true);
		$criteria->compare('t.gibdd_id',$this->gibdd_id,false);
		//
		//$criteria->addCondition('t.USER_ID='.$userid);
	
		$provider=new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				        'pageSize'=>$this->limit ? $this->limit : 12,				        
				    ),
			'sort'=>array(
			    'defaultOrder'=>'t.DATE_CREATED DESC',
				)
		));		

		return $provider;
	}		
	
	
	public function search($fixeds=false)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		//$criteria->with=Array('pictures_fresh','pictures_fixed');
		$criteria->with=Array('type','pictures_fresh', 'comments_cnt');
		
		if ($fixeds){
			$with=$criteria->with;
			$with[1]='pictures_fixed_not_moderated';
			$criteria->with=$with;
			$criteria->addCondition('t.STATE!="fixed"');
			$criteria->together=true;
		}
		else $criteria->compare('archive',$this->archive ? $this->archive : 0);
		
		if ($this->polygons){
			$corners=Array();
			foreach ($this->polygons as $i=>$polygon){
				$corners[$i]['left']=$polygon[0]['lng'];
				$corners[$i]['right']=$polygon[0]['lng'];
				$corners[$i]['top']=$polygon[0]['lat'];
				$corners[$i]['bottom']=$polygon[0]['lat'];
				foreach ($polygon as $point){
					if ($point['lng'] < $corners[$i]['left']) $corners[$i]['left']=$point['lng'];
					if ($point['lng'] > $corners[$i]['right']) $corners[$i]['right']=$point['lng'];
					if ($point['lat'] > $corners[$i]['top']) $corners[$i]['top']=$point['lat'];
					if ($point['lat'] < $corners[$i]['bottom']) $corners[$i]['bottom']=$point['lat'];
				}	
			}
			
			foreach ($corners as $corner){
				$cond='LONGITUDE >= '.$corner['left']
				.' AND LONGITUDE <= '.$corner['right']
				.' AND LATITUDE >= '.$corner['bottom']
				.' AND LATITUDE <= '.$corner['top'];					
				$criteria->addCondition($cond,'OR');			
			}
			
			$notPolygonHolesIds=$this->findPkeysNotInArea($this->polygons, $corners);
			if ($notPolygonHolesIds) $criteria->addNotInCondition('t.ID',$notPolygonHolesIds);				
		
		}
		
		$criteria->compare('t.deleted',0);
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
		if ($this->NOT_PREMODERATED) $criteria->compare('t.PREMODERATED',0);

		if (!Yii::app()->user->isModer) $criteria->compare('t.PREMODERATED',$this->PREMODERATED,true);
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
		$criteria->compare('t.deleted',$this->deleted,false);
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