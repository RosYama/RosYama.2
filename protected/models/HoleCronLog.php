<?php

/**
 * This is the model class for table "{{hole_cron_log}}".
 *
 * The followings are the available columns in table '{{hole_cron_log}}':
 * @property integer $id
 * @property string $type
 * @property integer $time_finish
 */
class HoleCronLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleCronLog the static model class
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
		return '{{hole_cron_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, time_finish', 'required'),
			array('time_finish', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, time_finish', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'time_finish' => 'Time Finish',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('time_finish',$this->time_finish);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}