<?php

/**
 * This is the model class for table "{{usergroups_user_hole_area}}".
 *
 * The followings are the available columns in table '{{usergroups_user_hole_area}}':
 * @property integer $id
 * @property integer $ug_id
 * @property integer $point_num
 * @property double $lat
 * @property double $lng
 */
class UserHoleArea extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UsergroupsUserHoleArea the static model class
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
		return '{{usergroups_user_hole_area}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ug_id, point_num, lat, lng', 'required'),
			array('ug_id, point_num', 'numerical', 'integerOnly'=>true),
			array('lat, lng', 'numerical'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ug_id, point_num, lat, lng', 'safe', 'on'=>'search'),
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
			'ug_id' => 'Ug',
			'point_num' => 'Point Num',
			'lat' => 'Lat',
			'lng' => 'Lng',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('ug_id',$this->ug_id);
		$criteria->compare('point_num',$this->point_num);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('lng',$this->lng);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}