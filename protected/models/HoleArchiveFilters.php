<?php

/**
 * This is the model class for table "{{hole_archive_filters}}".
 *
 * The followings are the available columns in table '{{hole_archive_filters}}':
 * @property integer $id
 * @property string $name
 * @property integer $type_id
 * @property string $status
 * @property integer $time_to
 */
class HoleArchiveFilters extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleArchiveFilters the static model class
	 */
	
	public function getTimeSelector(){
		return Array(
			0=>'не выбрано',
			60*60=>'1 час',
			60*60*24=>'1 день',
			60*60*24*7=>'1 неделю',
			60*60*24*7*2=>'2 недели',
			60*60*24*7*3=>'3 недели',
			60*60*24*30=>'1 месяц',
			60*60*24*30*2=>'2 месяца',
			60*60*24*30*3=>'3 месяца',
			60*60*24*30*4=>'4 месяца',
			60*60*24*30*5=>'5 месяцев',
			60*60*24*30*6=>'6 месяцев',
			60*60*24*30*7=>'7 месяцев',
			60*60*24*30*8=>'8 месяцев',
			60*60*24*30*9=>'9 месяцев',
			60*60*24*30*10=>'10 месяцев',
			60*60*24*30*11=>'11 месяцев',
			60*60*24*365=>'1 год',
			60*60*24*365*2=>'2 года',
			60*60*24*365*3=>'3 года',
			);
	}	
	 
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{hole_archive_filters}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('type_id, time_to', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('status', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, type_id, status, time_to', 'safe', 'on'=>'search'),
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
			'type'=>array(self::BELONGS_TO, 'HoleTypes', 'type_id'),
		);
	}
	
	public function BeforeSave(){
				parent::beforeSave();
				if (!$this->type_id) $this->type_id=0;
				return true;
	}	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название',
			'type_id' => 'Тип ям',
			'status' => 'Статус ям',
			'time_to' => 'Архивировать ямы с последним изменением раньше текущего времени на:',
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
		$criteria->compare('type_id',$this->type_id);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('time_to',$this->time_to);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}