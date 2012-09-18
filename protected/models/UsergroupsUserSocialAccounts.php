<?php

/**
 * This is the model class for table "{{usergroups_user_social_accounts}}".
 *
 * The followings are the available columns in table '{{usergroups_user_social_accounts}}':
 * @property integer $ug_id
 * @property integer $service_id
 * @property string $xml_id
 * @property string $external_auth_id
 */
class UsergroupsUserSocialAccounts extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UsergroupsUserSocialAccounts the static model class
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
		return '{{usergroups_user_social_accounts}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ug_id, service_id, xml_id, external_auth_id', 'required'),
			array('ug_id, service_id', 'numerical', 'integerOnly'=>true),
			array('xml_id, external_auth_id', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ug_id, service_id, xml_id, external_auth_id', 'safe', 'on'=>'search'),
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
			'user'=>array(self::BELONGS_TO, 'UserGroupsUser', 'ug_id'),
			'service'=>array(self::BELONGS_TO, 'UsergroupsSocialServices', 'service_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ug_id' => 'Ug',
			'service_id' => 'Service',
			'xml_id' => 'Xml',
			'external_auth_id' => 'External Auth',
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

		$criteria->compare('ug_id',$this->ug_id);
		$criteria->compare('service_id',$this->service_id);
		$criteria->compare('xml_id',$this->xml_id,true);
		$criteria->compare('external_auth_id',$this->external_auth_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}