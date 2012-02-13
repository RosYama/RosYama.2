<?php

/**
 * This is the model class for table "userGroups_group".
 *
 * The followings are the available columns in table 'userGroups_group':
 * @property string $id
 * @property string $groupname
 * @property string $level
 * @property string $home
 */
class UserGroupsGroup extends CActiveRecord
{
	
	/**
	 * contains the group access permission's array
	 * @var array
	 */
	public $access;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserGroupsGroup the static model class
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
		return Yii::app()->db->tablePrefix.'usergroups_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('groupname', 'length', 'max'=>120),
			array('groupname', 'unique'),
			array('level', 'levelCheck'),
			array('home', 'safe'),
			// rules used on creation
			array('groupname', 'required', 'on'=>'admin'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, groupname, level', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * If a new level value is provided it makes sure that it won't be higher then the
	 * one of the user updating or creating the group.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function levelCheck($attribute,$params)
	{
		// skip this check on installation
		if ($this->scenario === 'installation')
			return true;
		if ($this->$attribute >= Yii::app()->user->level)
			$this->addError('level', Yii::t('UserGroupsModule.admin','You cannot set a Group Level equal or superior to your own'));
		else if ($this->$attribute >= UserGroupsUser::ROOT_LEVEL)
			$this->addError('level', Yii::t('UserGroupsModule.admin','You cannot set a Group Level equal or higher then Root'));
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
			'id' => 'ID',
			'groupname' => Yii::t('UserGroupsModule.general','Group Name'),
			'level' => Yii::t('UserGroupsModule.general', 'Level'),
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
		$criteria->order = 'level DESC';
		$criteria->compare('id',$this->id,true);
		$criteria->compare('groupname',$this->groupname,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('level <',Yii::app()->user->level -1,false);
		
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize' => 10),
		));
	}
	
	/**
	 * parameters preparation after a select is executed
	 */
	public function afterFind()
	{
		// load the access permissions for the group
		$this->access = UserGroupsAccess::findRules(UserGroupsAccess::GROUP, $this->id);
		parent::afterFind();
	}
	
	/** 
	 * return the group array list
	 * @return Array
	 */
	public static function groupList()
	{
		$arrayData = array();
		$criteria=new CDbCriteria;
		$criteria->order = 'level DESC';
		$criteria->compare('level <',Yii::app()->user->level -1,false);
		$result = self::model()->findAll($criteria);
		foreach ($result as $r) {
			$arrayData[$r->id] = $r->groupname;
		}
		return $arrayData;
	}
}