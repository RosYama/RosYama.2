<?php

/**
 * @author Nicola Puddu
 * @package userGroups
 * This is the model class for table "userGroups_lookup".
 *
 * The followings are the available columns in table 'userGroups_lookup':
 * @property string $id
 * @property string $element
 * @property integer $value
 * @property string $text
 */
class UserGroupsLookup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserGroupsLookup the static model class
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
		return Yii::app()->db->tablePrefix.'usergroups_lookup';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('value', 'numerical', 'integerOnly'=>true),
			array('element', 'length', 'max'=>20),
			array('text', 'length', 'max'=>40),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, element, value, text', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'element' => 'Element',
			'value' => 'Value',
			'text' => 'Text',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('element',$this->element,true);
		$criteria->compare('value',$this->value);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * return the corrisponding text of a given value
	 * @param String $element
	 * @param Int $value
	 * @return String
	 */
	static public function resolve($element, $value)
	{
		$criteria=new CDbCriteria;
		$criteria->compare('element',$element);
		$criteria->compare('value',$value);
		$result = self::model()->find($criteria);
		return $result->text;
	}
	
	/**
	 * return the corrisponding value of a given text
	 * @param String $element
	 * @param String $text
	 * @return Int
	 */
	static public function inverse($element, $text)
	{
		// if no text is provided return NULL
		if (!$text)
			return NULL;
		
		$criteria=new CDbCriteria;
		$criteria->compare('element',$element);
		$criteria->compare('text',$text);
		$result = self::model()->find($criteria);
		if ($result)
			return $result->value;
		else
			return -1;
	}
}