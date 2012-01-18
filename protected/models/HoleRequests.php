<?php

/**
 * This is the model class for table "{{hole_requests}}".
 *
 * The followings are the available columns in table '{{hole_requests}}':
 * @property integer $id
 * @property integer $hole_id
 * @property integer $user_id
 * @property integer $gibdd_id
 * @property integer $date_sent
 * @property string $type
 */
class HoleRequests extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleRequests the static model class
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
		return '{{hole_requests}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hole_id, user_id, gibdd_id, date_sent, type', 'required'),
			array('hole_id, user_id, gibdd_id, date_sent', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, hole_id, user_id, gibdd_id, date_sent, type', 'safe', 'on'=>'search'),
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
			'answer'=>array(self::HAS_ONE, 'HoleAnswers', 'request_id','order'=>'date DESC'),
			'answers'=>array(self::HAS_MANY, 'HoleAnswers', 'request_id','order'=>'date DESC'),
			'hole'=>array(self::BELONGS_TO, 'Holes', 'hole_id'),
		);
	}
	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'hole_id' => 'Hole',
			'user_id' => 'User',
			'gibdd_id' => 'Gibdd',
			'date_sent' => 'Date Sent',
			'type' => 'Type',
		);
	}
	
	public function beforeDelete(){
		foreach ($this->answers as $answer) $answer->delete();
		return true;
	}
	
	public function afterDelete(){
		if (!count ($this->findAll('hole_id='.$this->hole_id.' AND type="'.$this->type.'"'))){
			if ($this->type=='gibdd') {
				$this->hole->STATE='inprogress';				
				$this->hole->update();
			}
			if ($this->type=='prosecutor') {
				$this->hole->STATE='achtung';
				$this->hole->DATE_SENT_PROSECUTOR=null;
				$this->hole->update();
			}			
		}	
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
		$criteria->compare('hole_id',$this->hole_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('gibdd_id',$this->gibdd_id);
		$criteria->compare('date_sent',$this->date_sent);
		$criteria->compare('type',$this->type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}