<?php

/**
 * This is the model class for table "{{rf_subjects}}".
 *
 * The followings are the available columns in table '{{rf_subjects}}':
 * @property integer $id
 * @property string $name
 * @property string $name_full
 * @property string $name_full_genitive
 */
class RfSubjects extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return RfSubjects the static model class
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
		return '{{rf_subjects}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, name_full, name_full_genitive', 'required'),
			array('name, name_full, name_full_genitive', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, name_full, name_full_genitive', 'safe', 'on'=>'search'),
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
		'gibdd'=>array(self::HAS_ONE, 'GibddHeads', 'subject_id'),
		'prosecutor'=>array(self::HAS_ONE, 'Prosecutors', 'subject_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'name_full' => 'Name Full',
			'name_full_genitive' => 'Name Full Genitive',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_full',$this->name_full,true);
		$criteria->compare('name_full_genitive',$this->name_full_genitive,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}