<?php

/**
 * This is the model class for table "{{user_area_shapes}}".
 *
 * The followings are the available columns in table '{{user_area_shapes}}':
 * @property integer $id
 * @property integer $ug_id
 */
class UserAreaShapes extends CActiveRecord
{
	public $countPoints=4;
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserAreaShapes the static model class
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
		return '{{user_area_shapes}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ug_id, ordering', 'required'),
			array('ug_id, ordering', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ug_id', 'safe', 'on'=>'search'),
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
		'points'=> array(self::HAS_MANY, 'UserAreaShapePoints', 'shape_id','order'=>'points.point_num'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ug_id' => 'Ug',
		);
	}
	
	public function beforeDelete(){
		parent::beforeDelete();
		foreach ($this->points as $point) $point->delete();
		return true;
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

		$criteria->compare('id',$this->id);
		$criteria->compare('ug_id',$this->ug_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}