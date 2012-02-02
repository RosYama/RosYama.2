<?php

/**
 * This is the model class for table "b_user_group".
 *
 * The followings are the available columns in table 'b_user_group':
 * @property integer $USER_ID
 * @property integer $GROUP_ID
 * @property string $DATE_ACTIVE_FROM
 * @property string $DATE_ACTIVE_TO
 */
class BUserGroup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return BUserGroup the static model class
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
		return 'b_user_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('USER_ID, GROUP_ID', 'required'),
			array('USER_ID, GROUP_ID', 'numerical', 'integerOnly'=>true),
			array('DATE_ACTIVE_FROM, DATE_ACTIVE_TO', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('USER_ID, GROUP_ID, DATE_ACTIVE_FROM, DATE_ACTIVE_TO', 'safe', 'on'=>'search'),
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
			'USER_ID' => 'User',
			'GROUP_ID' => 'Group',
			'DATE_ACTIVE_FROM' => 'Date Active From',
			'DATE_ACTIVE_TO' => 'Date Active To',
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

		$criteria->compare('USER_ID',$this->USER_ID);
		$criteria->compare('GROUP_ID',$this->GROUP_ID);
		$criteria->compare('DATE_ACTIVE_FROM',$this->DATE_ACTIVE_FROM,true);
		$criteria->compare('DATE_ACTIVE_TO',$this->DATE_ACTIVE_TO,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}