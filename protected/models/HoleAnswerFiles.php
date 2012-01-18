<?php

/**
 * This is the model class for table "{{hole_answer_files}}".
 *
 * The followings are the available columns in table '{{hole_answer_files}}':
 * @property integer $id
 * @property integer $answer_id
 * @property string $file_name
 * @property string $file_type
 */
class HoleAnswerFiles extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleAnswerFiles the static model class
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
		return '{{hole_answer_files}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('answer_id, file_name, file_type', 'required'),
			array('answer_id', 'numerical', 'integerOnly'=>true),
			array('file_name', 'length', 'max'=>511),
			array('file_type', 'length', 'max'=>63),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, answer_id, file_name, file_type', 'safe', 'on'=>'search'),
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
		'answer'=>array(self::BELONGS_TO, 'HoleAnswers', 'answer_id'),		
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'answer_id' => 'Answer',
			'file_name' => 'File Name',
			'file_type' => 'File Type',
		);
	}
	
	public function beforeDelete(){
		if ($this->file_type=='image')
			unlink($_SERVER['DOCUMENT_ROOT'].$this->answer->filesFolder.'/thumbs/'.$this->file_name);
			
		unlink($_SERVER['DOCUMENT_ROOT'].$this->answer->filesFolder.'/'.$this->file_name);
		return true;
	}
	
	public function afterDelete(){
		if (!count ($this->findAll('answer_id='.$this->answer_id)))
			$this->answer->delete();
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
		$criteria->compare('answer_id',$this->answer_id);
		$criteria->compare('file_name',$this->file_name,true);
		$criteria->compare('file_type',$this->file_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}