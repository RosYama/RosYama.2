<?php

/**
 * This is the model class for table "b_holes".
 *
 * The followings are the available columns in table 'b_holes':
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
 * @property string $TYPE
 * @property string $ADR_SUBJECTRF
 * @property string $ADR_CITY
 * @property string $COMMENT_GIBDD_REPLY
 * @property integer $GIBDD_REPLY_RECEIVED
 * @property integer $PREMODERATED
 * @property string $DATE_SENT_PROSECUTOR
 */
class BHoles extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return BHoles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'b_holes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('USER_ID, LATITUDE, LONGITUDE, ADDRESS, DATE_CREATED', 'required'),
			array('GIBDD_REPLY_RECEIVED, PREMODERATED', 'numerical', 'integerOnly'=>true),
			array('LATITUDE, LONGITUDE', 'numerical'),
			array('USER_ID, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, TYPE, ADR_SUBJECTRF, DATE_SENT_PROSECUTOR', 'length', 'max'=>10),
			array('ADR_CITY', 'length', 'max'=>50),
			array('COMMENT1, COMMENT2, COMMENT_GIBDD_REPLY', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, USER_ID, LATITUDE, LONGITUDE, ADDRESS, STATE, DATE_CREATED, DATE_SENT, DATE_STATUS, COMMENT1, COMMENT2, TYPE, ADR_SUBJECTRF, ADR_CITY, COMMENT_GIBDD_REPLY, GIBDD_REPLY_RECEIVED, PREMODERATED, DATE_SENT_PROSECUTOR', 'safe', 'on'=>'search'),
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
		);
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
	
	public function getPicturenames(){
	
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
							$v['original']['fixed'][] = $f;
							$v['medium']['fixed'][]   = $f;
							$v['small']['fixed'][]    = $f;
						}
						elseif(substr($f, 0, 2) == 'gr')
						{
							$v['original']['gibddreply'][] = $f;
							$v['medium']['gibddreply'][]   = $f;
							$v['small']['gibddreply'][]    = $f;
						}
						else
						{
							$v['original']['fresh'][] = $f;
							$v['medium']['fresh'][]   = $f;
							$v['small']['fresh'][]    = $f;
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
			'ADDRESS' => 'Address',
			'STATE' => 'State',
			'DATE_CREATED' => 'Date Created',
			'DATE_SENT' => 'Date Sent',
			'DATE_STATUS' => 'Date Status',
			'COMMENT1' => 'Comment1',
			'COMMENT2' => 'Comment2',
			'TYPE' => 'Type',
			'ADR_SUBJECTRF' => 'Adr Subjectrf',
			'ADR_CITY' => 'Adr City',
			'COMMENT_GIBDD_REPLY' => 'Comment Gibdd Reply',
			'GIBDD_REPLY_RECEIVED' => 'Gibdd Reply Received',
			'PREMODERATED' => 'Premoderated',
			'DATE_SENT_PROSECUTOR' => 'Date Sent Prosecutor',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
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
		$criteria->compare('TYPE',$this->TYPE,true);
		$criteria->compare('ADR_SUBJECTRF',$this->ADR_SUBJECTRF,true);
		$criteria->compare('ADR_CITY',$this->ADR_CITY,true);
		$criteria->compare('COMMENT_GIBDD_REPLY',$this->COMMENT_GIBDD_REPLY,true);
		$criteria->compare('GIBDD_REPLY_RECEIVED',$this->GIBDD_REPLY_RECEIVED);
		$criteria->compare('PREMODERATED',$this->PREMODERATED);
		$criteria->compare('DATE_SENT_PROSECUTOR',$this->DATE_SENT_PROSECUTOR,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}