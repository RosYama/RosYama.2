<?php

/**
 * This is the model class for table "{{user_selected_lists}}".
 *
 * The followings are the available columns in table '{{user_selected_lists}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $gibdd_id
 * @property integer $date_created
 */
class UserSelectedLists extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserSelectedLists the static model class
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
		return '{{user_selected_lists}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, gibdd_id, date_created', 'required'),
			array('user_id, gibdd_id, date_created', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, gibdd_id, date_created', 'safe', 'on'=>'search'),
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
		'holes'=>array(self::MANY_MANY, 'Holes',
               '{{user_selected_lists_holes_xref}}(list_id, hole_id)'),
		);
	}
	
	public function behaviors(){
          return array( 'CAdvancedArBehavior' => array(
            'class' => 'application.extensions.CAdvancedArBehavior'));
    }
    
    public function getNotSentHoles(){
    	$holes=Array();
		foreach ($this->holes as $hole)
			if (!$hole->request_gibdd) $holes[]=$hole;
		return $holes;	
    }
    
    public function getSentedHoles(){
    	$holes=Array();
		foreach ($this->holes as $hole)
			if ($hole->request_gibdd) $holes[]=$hole;
		return $holes;	
    }
    
	public function getSentedHolesHasAnswer(){
    	$holes=Array();
		foreach ($this->sentedHoles as $hole)
			if ($hole->request_gibdd->answer) $holes[]=$hole;
		return $holes;	
    }    
    
	public function getSentedHolesHasNoAnswer(){
    	$holes=Array();
		foreach ($this->sentedHoles as $hole)
			if (!$hole->request_gibdd->answer) $holes[]=$hole;
		return $holes;	
    }        
    
    public function beforeDelete(){
		parent::beforeDelete();
		$this->holes=Array();
		$this->update();
		return true;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'gibdd_id' => 'Gibdd',
			'date_created' => 'Date Created',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('gibdd_id',$this->gibdd_id);
		$criteria->compare('date_created',$this->date_created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}