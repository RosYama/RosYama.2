<?php

/**
 * @author Nicola Puddu
 * @package userGroups
 * This is the model class for table "usergroups_configuration".
 *
 * The followings are the available columns in table 'usergroups_configuration':
 * @property string $rule
 * @property string $value
 * @property string $options
 * @property string $description
 */
class UserGroupsConfiguration extends CActiveRecord
{
	/**
	 * html code of the single rule editing view. Usually contains the
	 * code of a Drop Down List with the possible values
	 * @var string
	 */
	public $render;

	/**
	 * Returns the static model of the specified AR class.
	 * @return UserGroupsConfiguration the static model class
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
		return Yii::app()->db->tablePrefix.'usergroups_configuration';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('rule, value', 'length', 'max'=>40),
			array('value', 'noCheating'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('rule, value', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * check if the data sended is valid and the user is not trying to cheat
	 * This is the 'noCheating' validator as declared in rules().
	 */
	public function noCheating($attribute,$params)
	{
		// skip this check on installation
		if ($this->scenario === 'installation')
			return true;
		// check user_registration_group configuration value
		if ($this->rule === 'user_registration_group') {
			$group = UserGroupsGroup::model()->findByPk((int)$this->value);
			if ($group === NULL)
				$this->addError('value',Yii::t('userGroupsModule.admin','This group does not exist'));
			elseif ((int)$group->level >= (int)Yii::app()->user->level)
				$this->addError('value',Yii::t('userGroupsModule.admin','You cannot set this value to a level equal or higher then your own'));
		}
		// check valid input for bool options, const and others
		if ($this->options === 'BOOL' && $this->value !== 'FALSE' && $this->value !== 'TRUE')
			$this->addError('value',Yii::t('userGroupsModule.admin','invalid value'));
		elseif ($this->options === 'CONST' && $this->scenario !== 'module_update')
			$this->addError('value',Yii::t('userGroupsModule.admin','You cannot change constant values'));
		elseif (strpos($this->options, 'a:') === 0) {
			$options_array = unserialize($this->options);
			if (!isset($options_array[$this->value]))
				$this->addError('value',Yii::t('userGroupsModule.admin','invalid value'));
		}
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
			'rule' => Yii::t('userGroupsModule.admin','Setting'),
			'value' => Yii::t('userGroupsModule.admin','Value'),
			'options' => Yii::t('userGroupsModule.admin','Options'),
			'description' => Yii::t('userGroupsModule.admin','Description'),
			'render' => Yii::t('userGroupsModule.admin','Value'),
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

		$criteria->compare('rule',$this->rule,true);
		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * parameters preparation after a select is executed
	 */
	public function afterFind()
	{
		switch ($this->options) {
			case (!Yii::app()->user->accessRules === UserGroupsUser::ROOT_ACCESS && !isset(Yii::app()->user->accessRules['userGroups']['admin']['admin'])):
				if ($this->options !== 'CONST' && $this->options !== 'BOOL' && $this->options !== 'GROUP_LIST') {
					$options = unserialize($this->options);
					$this->render = $options[$this->value];
				} else
					$this->render = $this->value;
				break;
			case 'CONST':
				$this->render = $this->value;
				break;
			case 'BOOL':
				$this->render = CHtml::dropDownList("UserGroupsConfiguration[$this->id]", $this->value, array('TRUE'=>'TRUE','FALSE'=>'FALSE'));
				break;
			case 'GROUP_LIST':
				$this->render = CHtml::dropDownList("UserGroupsConfiguration[$this->id]", $this->value, UserGroupsGroup::groupList());
				break;
			default:
				$this->render = CHtml::dropDownList("UserGroupsConfiguration[$this->id]", $this->value, unserialize($this->options));
				break;
		}



		if (Yii::app()->controller->module instanceof UserGroupsModule)
			$this->description = Yii::t('userGroupsModule.conf_description', ''.$this->description);

		parent::afterFind();
	}

	/**
	 * return the current rule value
	 * @param String $rule
	 * @return Mixed
	 */
	public static function findRule($rule)
	{
		$criteria=new CDbCriteria;
		$criteria->compare('rule',$rule);
		$model = self::model();
		$model->scenario = 'find_rule';
		$result = $model->find($criteria);
		if ($result !== NULL) {
			if ($result->value === 'TRUE')
				$result->value = true;
			if ($result->value === 'FALSE')
				$result->value = false;
			return $result->value;
		}else
			return false;
	}
}