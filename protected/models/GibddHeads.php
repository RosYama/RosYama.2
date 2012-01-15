<?php

/**
 * This is the model class for table "{{gibdd_heads}}".
 *
 * The followings are the available columns in table '{{gibdd_heads}}':
 * @property integer $id
 * @property string $name
 * @property integer $subject_id
 * @property string $post
 * @property string $post_dative
 * @property string $fio
 * @property string $fio_dative
 * @property string $gibdd_name
 * @property string $contacts
 * @property string $address
 * @property string $tel_degurn
 * @property string $tel_dover
 * @property string $url
 */
class GibddHeads extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return GibddHeads the static model class
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
		return '{{gibdd_heads}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, subject_id, post, post_dative, fio, fio_dative, gibdd_name, contacts, address, tel_degurn, tel_dover, url', 'required'),
			array('subject_id', 'numerical', 'integerOnly'=>true),
			array('post, post_dative, fio, fio_dative, gibdd_name, tel_degurn, tel_dover, url', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, subject_id, post, post_dative, fio, fio_dative, gibdd_name, contacts, address, tel_degurn, tel_dover, url', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'subject_id' => 'Subject',
			'post' => 'Post',
			'post_dative' => 'Post Dative',
			'fio' => 'Fio',
			'fio_dative' => 'Fio Dative',
			'gibdd_name' => 'Gibdd Name',
			'contacts' => 'Contacts',
			'address' => 'Address',
			'tel_degurn' => 'Tel Degurn',
			'tel_dover' => 'Tel Dover',
			'url' => 'Url',
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
		$criteria->compare('post',$this->post,true);
		$criteria->compare('post_dative',$this->post_dative,true);
		$criteria->compare('fio',$this->fio,true);
		$criteria->compare('fio_dative',$this->fio_dative,true);
		$criteria->compare('gibdd_name',$this->gibdd_name,true);
		$criteria->compare('contacts',$this->contacts,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('tel_degurn',$this->tel_degurn,true);
		$criteria->compare('tel_dover',$this->tel_dover,true);
		$criteria->compare('url',$this->url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}