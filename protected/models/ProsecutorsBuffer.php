<?php

/**
 * This is the model class for table "{{prosecutors}}".
 *
 * The followings are the available columns in table '{{prosecutors}}':
 * @property integer $id
 * @property string $name
 * @property integer $subject_id
 * @property string $preview_text
 * @property string $gibdd_name
 */
class ProsecutorsBuffer extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Prosecutors the static model class
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
		return '{{prosecutors_buffer}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, subject_id, preview_text, gibdd_name', 'required'),
			array('subject_id', 'numerical', 'integerOnly'=>true),
			array('name, gibdd_name, tel_chancery', 'length', 'max'=>255),
			array('url_priemnaya', 'url','allowEmpty'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, subject_id, preview_text, gibdd_name', 'safe', 'on'=>'search'),
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
			'name' => 'Расширенное название для вставки в заявление',
			'subject_id' => 'Subject',
			'preview_text' => 'Описание',
			'gibdd_name' => 'Название',
			'url_priemnaya'=>'Интернет-приемная',
			'tel_chancery' => 'Тел. канцелярии',
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
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('preview_text',$this->preview_text,true);
		$criteria->compare('gibdd_name',$this->gibdd_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}