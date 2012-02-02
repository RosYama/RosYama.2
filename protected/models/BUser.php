<?php

/**
 * This is the model class for table "b_user".
 *
 * The followings are the available columns in table 'b_user':
 * @property integer $ID
 * @property string $TIMESTAMP_X
 * @property string $LOGIN
 * @property string $PASSWORD
 * @property string $CHECKWORD
 * @property string $ACTIVE
 * @property string $NAME
 * @property string $LAST_NAME
 * @property string $EMAIL
 * @property string $LAST_LOGIN
 * @property string $DATE_REGISTER
 * @property string $LID
 * @property string $PERSONAL_PROFESSION
 * @property string $PERSONAL_WWW
 * @property string $PERSONAL_ICQ
 * @property string $PERSONAL_GENDER
 * @property string $PERSONAL_BIRTHDATE
 * @property integer $PERSONAL_PHOTO
 * @property string $PERSONAL_PHONE
 * @property string $PERSONAL_FAX
 * @property string $PERSONAL_MOBILE
 * @property string $PERSONAL_PAGER
 * @property string $PERSONAL_STREET
 * @property string $PERSONAL_MAILBOX
 * @property string $PERSONAL_CITY
 * @property string $PERSONAL_STATE
 * @property string $PERSONAL_ZIP
 * @property string $PERSONAL_COUNTRY
 * @property string $PERSONAL_NOTES
 * @property string $WORK_COMPANY
 * @property string $WORK_DEPARTMENT
 * @property string $WORK_POSITION
 * @property string $WORK_WWW
 * @property string $WORK_PHONE
 * @property string $WORK_FAX
 * @property string $WORK_PAGER
 * @property string $WORK_STREET
 * @property string $WORK_MAILBOX
 * @property string $WORK_CITY
 * @property string $WORK_STATE
 * @property string $WORK_ZIP
 * @property string $WORK_COUNTRY
 * @property string $WORK_PROFILE
 * @property integer $WORK_LOGO
 * @property string $WORK_NOTES
 * @property string $ADMIN_NOTES
 * @property string $STORED_HASH
 * @property string $XML_ID
 * @property string $PERSONAL_BIRTHDAY
 * @property string $EXTERNAL_AUTH_ID
 * @property string $CHECKWORD_TIME
 * @property string $SECOND_NAME
 * @property string $CONFIRM_CODE
 * @property integer $LOGIN_ATTEMPTS
 * @property string $LAST_ACTIVITY_DATE
 * @property string $AUTO_TIME_ZONE
 * @property string $TIME_ZONE
 */
class BUser extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return BUser the static model class
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
		return 'b_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('TIMESTAMP_X, LOGIN, PASSWORD, EMAIL, DATE_REGISTER', 'required'),
			array('PERSONAL_PHOTO, WORK_LOGO, LOGIN_ATTEMPTS', 'numerical', 'integerOnly'=>true),
			array('LOGIN, PASSWORD, CHECKWORD, NAME, LAST_NAME, PERSONAL_BIRTHDATE, SECOND_NAME, TIME_ZONE', 'length', 'max'=>50),
			array('ACTIVE, PERSONAL_GENDER, AUTO_TIME_ZONE', 'length', 'max'=>1),
			array('EMAIL, PERSONAL_PROFESSION, PERSONAL_WWW, PERSONAL_ICQ, PERSONAL_PHONE, PERSONAL_FAX, PERSONAL_MOBILE, PERSONAL_PAGER, PERSONAL_MAILBOX, PERSONAL_CITY, PERSONAL_STATE, PERSONAL_ZIP, PERSONAL_COUNTRY, WORK_COMPANY, WORK_DEPARTMENT, WORK_POSITION, WORK_WWW, WORK_PHONE, WORK_FAX, WORK_PAGER, WORK_MAILBOX, WORK_CITY, WORK_STATE, WORK_ZIP, WORK_COUNTRY, XML_ID, EXTERNAL_AUTH_ID', 'length', 'max'=>255),
			array('LID', 'length', 'max'=>2),
			array('STORED_HASH', 'length', 'max'=>32),
			array('CONFIRM_CODE', 'length', 'max'=>8),
			array('LAST_LOGIN, PERSONAL_STREET, PERSONAL_NOTES, WORK_STREET, WORK_PROFILE, WORK_NOTES, ADMIN_NOTES, PERSONAL_BIRTHDAY, CHECKWORD_TIME, LAST_ACTIVITY_DATE', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, TIMESTAMP_X, LOGIN, PASSWORD, CHECKWORD, ACTIVE, NAME, LAST_NAME, EMAIL, LAST_LOGIN, DATE_REGISTER, LID, PERSONAL_PROFESSION, PERSONAL_WWW, PERSONAL_ICQ, PERSONAL_GENDER, PERSONAL_BIRTHDATE, PERSONAL_PHOTO, PERSONAL_PHONE, PERSONAL_FAX, PERSONAL_MOBILE, PERSONAL_PAGER, PERSONAL_STREET, PERSONAL_MAILBOX, PERSONAL_CITY, PERSONAL_STATE, PERSONAL_ZIP, PERSONAL_COUNTRY, PERSONAL_NOTES, WORK_COMPANY, WORK_DEPARTMENT, WORK_POSITION, WORK_WWW, WORK_PHONE, WORK_FAX, WORK_PAGER, WORK_STREET, WORK_MAILBOX, WORK_CITY, WORK_STATE, WORK_ZIP, WORK_COUNTRY, WORK_PROFILE, WORK_LOGO, WORK_NOTES, ADMIN_NOTES, STORED_HASH, XML_ID, PERSONAL_BIRTHDAY, EXTERNAL_AUTH_ID, CHECKWORD_TIME, SECOND_NAME, CONFIRM_CODE, LOGIN_ATTEMPTS, LAST_ACTIVITY_DATE, AUTO_TIME_ZONE, TIME_ZONE', 'safe', 'on'=>'search'),
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'TIMESTAMP_X' => 'Timestamp X',
			'LOGIN' => 'Login',
			'PASSWORD' => 'Password',
			'CHECKWORD' => 'Checkword',
			'ACTIVE' => 'Active',
			'NAME' => 'Name',
			'LAST_NAME' => 'Last Name',
			'EMAIL' => 'Email',
			'LAST_LOGIN' => 'Last Login',
			'DATE_REGISTER' => 'Date Register',
			'LID' => 'Lid',
			'PERSONAL_PROFESSION' => 'Personal Profession',
			'PERSONAL_WWW' => 'Personal Www',
			'PERSONAL_ICQ' => 'Personal Icq',
			'PERSONAL_GENDER' => 'Personal Gender',
			'PERSONAL_BIRTHDATE' => 'Personal Birthdate',
			'PERSONAL_PHOTO' => 'Personal Photo',
			'PERSONAL_PHONE' => 'Personal Phone',
			'PERSONAL_FAX' => 'Personal Fax',
			'PERSONAL_MOBILE' => 'Personal Mobile',
			'PERSONAL_PAGER' => 'Personal Pager',
			'PERSONAL_STREET' => 'Personal Street',
			'PERSONAL_MAILBOX' => 'Personal Mailbox',
			'PERSONAL_CITY' => 'Personal City',
			'PERSONAL_STATE' => 'Personal State',
			'PERSONAL_ZIP' => 'Personal Zip',
			'PERSONAL_COUNTRY' => 'Personal Country',
			'PERSONAL_NOTES' => 'Personal Notes',
			'WORK_COMPANY' => 'Work Company',
			'WORK_DEPARTMENT' => 'Work Department',
			'WORK_POSITION' => 'Work Position',
			'WORK_WWW' => 'Work Www',
			'WORK_PHONE' => 'Work Phone',
			'WORK_FAX' => 'Work Fax',
			'WORK_PAGER' => 'Work Pager',
			'WORK_STREET' => 'Work Street',
			'WORK_MAILBOX' => 'Work Mailbox',
			'WORK_CITY' => 'Work City',
			'WORK_STATE' => 'Work State',
			'WORK_ZIP' => 'Work Zip',
			'WORK_COUNTRY' => 'Work Country',
			'WORK_PROFILE' => 'Work Profile',
			'WORK_LOGO' => 'Work Logo',
			'WORK_NOTES' => 'Work Notes',
			'ADMIN_NOTES' => 'Admin Notes',
			'STORED_HASH' => 'Stored Hash',
			'XML_ID' => 'Xml',
			'PERSONAL_BIRTHDAY' => 'Personal Birthday',
			'EXTERNAL_AUTH_ID' => 'External Auth',
			'CHECKWORD_TIME' => 'Checkword Time',
			'SECOND_NAME' => 'Second Name',
			'CONFIRM_CODE' => 'Confirm Code',
			'LOGIN_ATTEMPTS' => 'Login Attempts',
			'LAST_ACTIVITY_DATE' => 'Last Activity Date',
			'AUTO_TIME_ZONE' => 'Auto Time Zone',
			'TIME_ZONE' => 'Time Zone',
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

		$criteria->compare('ID',$this->ID);
		$criteria->compare('TIMESTAMP_X',$this->TIMESTAMP_X,true);
		$criteria->compare('LOGIN',$this->LOGIN,true);
		$criteria->compare('PASSWORD',$this->PASSWORD,true);
		$criteria->compare('CHECKWORD',$this->CHECKWORD,true);
		$criteria->compare('ACTIVE',$this->ACTIVE,true);
		$criteria->compare('NAME',$this->NAME,true);
		$criteria->compare('LAST_NAME',$this->LAST_NAME,true);
		$criteria->compare('EMAIL',$this->EMAIL,true);
		$criteria->compare('LAST_LOGIN',$this->LAST_LOGIN,true);
		$criteria->compare('DATE_REGISTER',$this->DATE_REGISTER,true);
		$criteria->compare('LID',$this->LID,true);
		$criteria->compare('PERSONAL_PROFESSION',$this->PERSONAL_PROFESSION,true);
		$criteria->compare('PERSONAL_WWW',$this->PERSONAL_WWW,true);
		$criteria->compare('PERSONAL_ICQ',$this->PERSONAL_ICQ,true);
		$criteria->compare('PERSONAL_GENDER',$this->PERSONAL_GENDER,true);
		$criteria->compare('PERSONAL_BIRTHDATE',$this->PERSONAL_BIRTHDATE,true);
		$criteria->compare('PERSONAL_PHOTO',$this->PERSONAL_PHOTO);
		$criteria->compare('PERSONAL_PHONE',$this->PERSONAL_PHONE,true);
		$criteria->compare('PERSONAL_FAX',$this->PERSONAL_FAX,true);
		$criteria->compare('PERSONAL_MOBILE',$this->PERSONAL_MOBILE,true);
		$criteria->compare('PERSONAL_PAGER',$this->PERSONAL_PAGER,true);
		$criteria->compare('PERSONAL_STREET',$this->PERSONAL_STREET,true);
		$criteria->compare('PERSONAL_MAILBOX',$this->PERSONAL_MAILBOX,true);
		$criteria->compare('PERSONAL_CITY',$this->PERSONAL_CITY,true);
		$criteria->compare('PERSONAL_STATE',$this->PERSONAL_STATE,true);
		$criteria->compare('PERSONAL_ZIP',$this->PERSONAL_ZIP,true);
		$criteria->compare('PERSONAL_COUNTRY',$this->PERSONAL_COUNTRY,true);
		$criteria->compare('PERSONAL_NOTES',$this->PERSONAL_NOTES,true);
		$criteria->compare('WORK_COMPANY',$this->WORK_COMPANY,true);
		$criteria->compare('WORK_DEPARTMENT',$this->WORK_DEPARTMENT,true);
		$criteria->compare('WORK_POSITION',$this->WORK_POSITION,true);
		$criteria->compare('WORK_WWW',$this->WORK_WWW,true);
		$criteria->compare('WORK_PHONE',$this->WORK_PHONE,true);
		$criteria->compare('WORK_FAX',$this->WORK_FAX,true);
		$criteria->compare('WORK_PAGER',$this->WORK_PAGER,true);
		$criteria->compare('WORK_STREET',$this->WORK_STREET,true);
		$criteria->compare('WORK_MAILBOX',$this->WORK_MAILBOX,true);
		$criteria->compare('WORK_CITY',$this->WORK_CITY,true);
		$criteria->compare('WORK_STATE',$this->WORK_STATE,true);
		$criteria->compare('WORK_ZIP',$this->WORK_ZIP,true);
		$criteria->compare('WORK_COUNTRY',$this->WORK_COUNTRY,true);
		$criteria->compare('WORK_PROFILE',$this->WORK_PROFILE,true);
		$criteria->compare('WORK_LOGO',$this->WORK_LOGO);
		$criteria->compare('WORK_NOTES',$this->WORK_NOTES,true);
		$criteria->compare('ADMIN_NOTES',$this->ADMIN_NOTES,true);
		$criteria->compare('STORED_HASH',$this->STORED_HASH,true);
		$criteria->compare('XML_ID',$this->XML_ID,true);
		$criteria->compare('PERSONAL_BIRTHDAY',$this->PERSONAL_BIRTHDAY,true);
		$criteria->compare('EXTERNAL_AUTH_ID',$this->EXTERNAL_AUTH_ID,true);
		$criteria->compare('CHECKWORD_TIME',$this->CHECKWORD_TIME,true);
		$criteria->compare('SECOND_NAME',$this->SECOND_NAME,true);
		$criteria->compare('CONFIRM_CODE',$this->CONFIRM_CODE,true);
		$criteria->compare('LOGIN_ATTEMPTS',$this->LOGIN_ATTEMPTS);
		$criteria->compare('LAST_ACTIVITY_DATE',$this->LAST_ACTIVITY_DATE,true);
		$criteria->compare('AUTO_TIME_ZONE',$this->AUTO_TIME_ZONE,true);
		$criteria->compare('TIME_ZONE',$this->TIME_ZONE,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}