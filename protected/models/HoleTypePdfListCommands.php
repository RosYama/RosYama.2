<?php

/**
 * This is the model class for table "{{hole_type_pdf_list_commands}}".
 *
 * The followings are the available columns in table '{{hole_type_pdf_list_commands}}':
 * @property integer $hole_type_id
 * @property string $text
 * @property integer $ordering
 */
class HoleTypePdfListCommands extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleTypePdfListCommands the static model class
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
		return '{{hole_type_pdf_list_commands}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hole_type_id, text, ordering', 'required'),
			array('hole_type_id, ordering', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('hole_type_id, text, ordering', 'safe', 'on'=>'search'),
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
			'hole_type_id' => 'Hole Type',
			'text' => 'Требование',
			'ordering' => 'Ordering',
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

		$criteria->compare('hole_type_id',$this->hole_type_id);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}