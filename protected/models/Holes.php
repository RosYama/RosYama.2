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

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{holes}}';
	}
	
	public $ADR_CITY='Город';
	
	public function getMessages(){
		return array(
			'GREENSIGHT_ERROR_NOID'=>'Элемент не найден',
			'GREENSIGHT_ERROR_UNSUPPORTED_IMAGE_TYPE'=>'Неподдерживаемый формат изображения',
			'GREENSIGHT_ERROR_DATABASE'=>'Ошибка базы данных :(',
			'GREENSIGHT_ERROR_CANNOT_CREATE_DIR'=>'Ошибка при загрузке файла',
			'GREENSIGHT_ERROR_RAPIDFIRE'=>'Дефекты нельзя добавлять слишком часто',
		);
	}
	
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
			array('USER_ID, LATITUDE, LONGITUDE, ADDRESS, DATE_CREATED, TYPE_ID', 'required'),
			array('GIBDD_REPLY_RECEIVED, PREMODERATED, TYPE_ID, NOT_PREMODERATED', 'numerical', 'integerOnly'=>true),
			array('LATITUDE, LONGITUDE', 'numerical'),
			array('USER_ID, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, ADR_SUBJECTRF, DATE_SENT_PROSECUTOR', 'length', 'max'=>10),
			array('ADR_CITY', 'length', 'max'=>50),
			array('STR_SUBJECTRF', 'length'),
			array('COMMENT1, COMMENT2, COMMENT_GIBDD_REPLY, deletepict, replуfiles, request_gibdd', 'safe'),			
			array('UpploadedPictures', 'required', 'on' => 'insert', 'message' => 'Необходимо загрузить фотографии'),
			array('replуfiles', 'required', 'on' => 'gibdd_reply', 'message' => 'Необходимо загрузить ответ ГИБДД'),
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
			'request_gibdd'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_gibdd.type="gibdd"'),
			'request_prosecutor'=>array(self::HAS_ONE, 'HoleRequests', 'hole_id', 'condition'=>'request_gibdd.prosecutor="prosecutor"'),
			'type'=>array(self::BELONGS_TO, 'HoleTypes', 'TYPE_ID'),
			'user'=>array(self::BELONGS_TO, 'UserGroupsUser', 'USER_ID'),		
		);
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
	$arr['gibddre']    = 'В ГАИ';
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
		if(($this->STATE == 'inprogress' || $this->STATE == 'achtung') && $this->DATE_SENT && !$this->STATE != 'gibddre')
		{
			$this->WAIT_DAYS = 38 - ceil((time() - $this->DATE_SENT) / 86400);	
			if ($this->WAIT_DAYS<0) { 
			$this->PAST_DAYS=abs($this->WAIT_DAYS);
			$this->WAIT_DAYS=0;
			}
		}
		
		
		
		if ($this->WAIT_DAYS < 0 && $this->STATE == 'inprogress') {
			$this->STATE = 'achtung';
			$this->update();
			}
		return $this->AllstatesShort[$this->STATE];
	}
	
	public function getPictures(){
	
		// картинки
		if(!isset($_GET['nopicts']))
		{
		$v=Array();	
		$v['original']['fixed']=Array();
		$v['medium']['fixed']=Array();
		$v['small']['fixed']=Array();
		$v['original']['gibddreply']=Array();
		$v['medium']['gibddreply']=Array();
		$v['small']['gibddreply']=Array();
		$v['original']['fresh']=Array();
		$v['medium']['fresh']=Array();
		$v['small']['fresh']=Array();
		
			$k = $this->ID;
			
				$dir = opendir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$k);
				while($f = readdir($dir))
				{
					if($f != '.' && $f != '..')
					{
						if($f[0] == 'f')
						{
							$v['original']['fixed'][] = '/upload/st1234/original/'.$k.'/'.$f;
							$v['medium']['fixed'][]   = '/upload/st1234/medium/'.$k.'/'.$f;
							$v['small']['fixed'][]    = '/upload/st1234/small/'.$k.'/'.$f;
						}
						elseif(substr($f, 0, 2) == 'gr')
						{
							$v['original']['gibddreply'][] = '/upload/st1234/original/'.$k.'/'.$f;
							$v['medium']['gibddreply'][]   = '/upload/st1234/medium/'.$k.'/'.$f;
							$v['small']['gibddreply'][]    = '/upload/st1234/small/'.$k.'/'.$f;
						}
						else
						{
							$v['original']['fresh'][] = '/upload/st1234/original/'.$k.'/'.$f;
							$v['medium']['fresh'][]   = '/upload/st1234/medium/'.$k.'/'.$f;
							$v['small']['fresh'][]    = '/upload/st1234/small/'.$k.'/'.$f;
						}
					}
				}
				if ($f) closedir($f);
				sort($v['small']['fresh']);
				sort($v['medium']['fresh']);
				sort($v['original']['fresh']);
				sort($v['small']['gibddreply']);
				sort($v['medium']['gibddreply']);
				sort($v['original']['gibddreply']);
				sort($v['small']['fixed']);
				sort($v['medium']['fixed']);
				sort($v['original']['fixed']);
			}
		
		return $v;	
	
	}
	
	public function getUpploadedPictures(){
		return CUploadedFile::getInstancesByName('PictureFiles');
	}
	
	public function savePictures(){
						//echo count($this->deletepict);						
						$imagess=$this->UpploadedPictures;
						$id=$this->ID;
						if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id)){
							if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id))
							{
								$this->addError('pictures', $this->messages['GREENSIGHT_ERROR_CANNOT_CREATE_DIR']);
								return false;
							}
							if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$id))
							{
								unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id);
								$this->addError('pictures',$this->messages['GREENSIGHT_ERROR_CANNOT_CREATE_DIR']);
								return false;
							}
							if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/small/'.$id))
							{
								unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id);
								unlink($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$id);
								$this->addError('pictures',$this->messages['GREENSIGHT_ERROR_CANNOT_CREATE_DIR']);
								return false;
							}
						}						
						$_params=$this->params;
						$file_counter = 0;
						$k = $this->ID;			
						$pictdir=$_SERVER['DOCUMENT_ROOT'].'/upload/st1234/';
						$dir = scandir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$k);
						foreach ($dir as $f)
						{
							if($f != '.' && $f != '..')
							{							
							$pictid=(int)$f;
							if (isset($this->deletepict[$pictid]) && $this->deletepict[$pictid]==1){
								unlink($pictdir.'original/'.$k.'/'.$f);
								unlink($pictdir.'medium/'.$k.'/'.$f);
								unlink($pictdir.'small/'.$k.'/'.$f);	
								}
							}
						}						
						if ($f) {
						if (isset($pictid)) $file_counter=$pictid;
						//closedir($f);
						$file_counter++;
						}
						
				        foreach ($imagess as $_file){
							if(!$_file->hasError)
							{	
								$image = $this->imagecreatefromfile($_file->getTempName(), &$_image_info);
								if(!$image)
								{
									$this->addError('pictures',$this->messages['GREENSIGHT_ERROR_UNSUPPORTED_IMAGE_TYPE']);
									return false;
								}
								$aspect = max($_image_info[0] / $_params['big_sizex'], $_image_info[1] / $_params['big_sizey']);
								if($aspect > 1)
								{
									$new_x    = floor($_image_info[0] / $aspect);
									$new_y    = floor($_image_info[1] / $aspect);
									$newimage = imagecreatetruecolor($new_x, $new_y);
									imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_x, $new_y, $_image_info[0], $_image_info[1]);
									imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id.'/'.$file_counter.'.jpg');
								}
								else
								{
									imagejpeg($image, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$id.'/'.$file_counter.'.jpg');
								}
								$aspect   = max($_image_info[0] / $_params['medium_sizex'], $_image_info[1] / $_params['medium_sizey']);
								$new_x    = floor($_image_info[0] / $aspect);
								$new_y    = floor($_image_info[1] / $aspect);
								$newimage = imagecreatetruecolor($new_x, $new_y);
								imagecopyresampled($newimage, $image, 0, 0, 0, 0, $new_x, $new_y, $_image_info[0], $_image_info[1]);
								imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/medium/'.$id.'/'.$file_counter.'.jpg');
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
								imagejpeg($newimage, $_SERVER['DOCUMENT_ROOT'].'/upload/st1234/small/'.$id.'/'.$file_counter.'.jpg');
								imagedestroy($newimage);
								imagedestroy($image);
								$file_counter++;
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
	
	public function getReplуfiles(){
		return CUploadedFile::getInstancesByName('Holes[replуfiles]');
	}	
	
	public function updateSetinprogress()
	{
		if($this->STATE != 'fresh' && !($this->STATE == 'fixed' && !sizeof($this->pictures['original']['fixed'])))
				{
					return false;
				}
		else {
				$this->DATE_STATUS=time();
				if($this->STATE == 'fresh')  
				{
					$this->DATE_SENT = time(); 
					$this->STATE='inprogress';
					if (!$this->request_gibdd){
						$request=new HoleRequests;
						$request->attributes=Array(
							'hole_id'=>$this->ID,
							'user_id'=>Yii::app()->user->id,
							'gibdd_id'=>$this->subject ? $this->subject->gibdd->id : 0,
							'date_sent'=>time(),
							'type'=>'gibdd'
							);
						$request->save();	
							}
				}
				else
				{
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
						$this->request_gibdd=Array();
					}
				}
			if ($this->update()) return true;
			else return false;
		}	
	}
	
	public function updateRevoke()
	{
			if($this->STATE != 'inprogress')
				{
					return false;	
				}
				$this->DATE_STATUS = time();
				$this->STATE       = 'fresh';
			if ($this->update()) return true;
			else return false;
	}		
	
	public function BeforeDelete(){
				//сначала удаляем все картинки
				$k = $this->ID;			
				$pictdir=$_SERVER['DOCUMENT_ROOT'].'/upload/st1234/';
				$dir = opendir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/original/'.$k);
				while($f = readdir($dir))
				{
					if($f != '.' && $f != '..')
					{
					unlink($pictdir.'original/'.$k.'/'.$f);
					unlink($pictdir.'medium/'.$k.'/'.$f);
					unlink($pictdir.'small/'.$k.'/'.$f);					
					}
				}
				if ($f) closedir($f);		
				rmdir($pictdir.'original/'.$k);
				rmdir($pictdir.'medium/'.$k);
				rmdir($pictdir.'small/'.$k);

				return true;
	}	
	
	public function getIsUserHole(){				
				if ($this->USER_ID==Yii::app()->user->id) return true;
				else return false;
	}	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'USER_ID' => 'User',
			'LATITUDE' => 'Latitude',
			'LONGITUDE' => 'Longitude',
			'ADDRESS' => 'Адрес дефекта',
			'STATE' => 'State',
			'DATE_CREATED' => 'Date Created',
			'DATE_SENT' => 'Date Sent',
			'DATE_STATUS' => 'Date Status',
			'COMMENT1' => 'Комментарии',
			'COMMENT2' => 'Комментарии (по желанию)',
			'TYPE_ID' => 'Тип дефекта',
			'ADR_SUBJECTRF' => 'Adr Subjectrf',
			'ADR_CITY' => 'Adr City',
			'COMMENT_GIBDD_REPLY' => 'Comment Gibdd Reply',
			'GIBDD_REPLY_RECEIVED' => 'Gibdd Reply Received',
			'PREMODERATED' => 'Premoderated',
			'NOT_PREMODERATED' => 'только непроверенные',
			'DATE_SENT_PROSECUTOR' => 'Date Sent Prosecutor',
			'deletepict'=>'Удалить фотографию?',
			'replуfiles'=>'Необходимо добавить отсканированный ответ из ГИБДД'
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
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ID',$this->ID,true);
		$criteria->compare('USER_ID',$this->USER_ID,true);
		$criteria->compare('LATITUDE',$this->LATITUDE);
		$criteria->compare('LONGITUDE',$this->LONGITUDE);
		$criteria->compare('ADDRESS',$this->ADDRESS,true);
		$criteria->compare('STATE',$this->STATE,true);
		$criteria->compare('DATE_CREATED',$this->DATE_CREATED,true);
		$criteria->compare('DATE_SENT',$this->DATE_SENT,true);
		$criteria->compare('DATE_STATUS',$this->DATE_STATUS,true);
		$criteria->compare('COMMENT1',$this->COMMENT1,true);
		$criteria->compare('COMMENT2',$this->COMMENT2,true);
		$criteria->compare('TYPE_ID',$this->TYPE_ID,true);
		$criteria->compare('ADR_SUBJECTRF',$this->ADR_SUBJECTRF,true);
		$criteria->compare('ADR_CITY',$this->ADR_CITY,true);
		$criteria->compare('COMMENT_GIBDD_REPLY',$this->COMMENT_GIBDD_REPLY,true);
		$criteria->compare('GIBDD_REPLY_RECEIVED',$this->GIBDD_REPLY_RECEIVED);
		if ($this->NOT_PREMODERATED) $criteria->compare('PREMODERATED',0);
		if (!Yii::app()->user->isModer) $criteria->compare('PREMODERATED',1);
		$criteria->compare('DATE_SENT_PROSECUTOR',$this->DATE_SENT_PROSECUTOR,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}