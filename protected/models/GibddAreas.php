<?php

/**
 * This is the model class for table "{{gibdd_areas}}".
 *
 * The followings are the available columns in table '{{gibdd_areas}}':
 * @property integer $id
 * @property integer $gibdd_id
 */
class GibddAreas extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return GibddAreas the static model class
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
		return '{{gibdd_areas}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gibdd_id', 'required'),
			array('gibdd_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, gibdd_id', 'safe', 'on'=>'search'),
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
			'points'=>array(self::HAS_MANY, 'GibddAreaPoints', 'area_id', 'order'=>'points.point_num'),
		);
	}
	
	public function getJsAreaPoints(){
		$arr=Array();
		foreach ($this->points as $i=>$point){
			$arr[]='new YMaps.GeoPoint('.$point->lng.','.$point->lat.')';			
		}
		return implode(', ',$arr);
	}
	
	public function BeforeDelete(){
		
		parent::beforeDelete();
				
		GibddAreaPoints::model()->deleteAll('area_id='.$this->id);
			
		return true;
	}	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'gibdd_id' => 'Gibdd',
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
		$criteria->compare('gibdd_id',$this->gibdd_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}