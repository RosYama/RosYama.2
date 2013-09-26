<?php

/**
 * This is the model class for table "{{hole_pictures}}".
 *
 * The followings are the available columns in table '{{hole_pictures}}':
 * @property integer $id
 * @property integer $hole_id
 * @property string $type
 * @property string $filename
 * @property integer $ordering
 */
class HolePictures extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HolePictures the static model class
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
		return '{{hole_pictures}}';
	}
	
	public function getOriginal()
	{
		return '/upload/st1234/'.$this->hole->bigFolder.'/original/'.$this->hole_id.'/'.$this->filename;
	}
	public function getMedium()
	{
		return '/upload/st1234/'.$this->hole->bigFolder.'/medium/'.$this->hole_id.'/'.$this->filename;
	}
	public function getSmall()
	{
		return !Yii::app()->params['EnablePhotoRiot'] ? '/upload/st1234/'.$this->hole->bigFolder.'/small/'.$this->hole_id.'/'.$this->filename : '/images/small-black-hole.jpg';
	}
	
	public function getBinary($size='original')
	{
		$img_src = $_SERVER['DOCUMENT_ROOT'].$this->$size;

		$imgbinary = fread(fopen($img_src, "r"), filesize($img_src));
		
		return $imgbinary; 
	}
	
	public function getMime($size='original')
	{
		return CFileHelper::getMimeType($_SERVER['DOCUMENT_ROOT'].$this->$size);
	}
	
	public function getExtension($size='original')
	{
		return CFileHelper::getExtension($_SERVER['DOCUMENT_ROOT'].$this->$size);
	}
	
	public function getLastOrder() 
		{
        $criteria = new CDbCriteria();
		$criteria->select='ordering';
		$criteria->limit=1;
		$criteria->order='ordering DESC';
		$criteria->condition='hole_id='.$this->hole_id.' AND type="'.$this->type.'"';
		$lastorder=$this->find($criteria);
        if ($lastorder) return $lastorder->ordering;
        else return 0;
		}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hole_id, type, filename, ordering', 'required'),
			array('hole_id, ordering, user_id, premoderated', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>63),
			array('filename', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, hole_id, type, filename, ordering', 'safe', 'on'=>'search'),
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
		'hole'=>array(self::BELONGS_TO, 'Holes', 'hole_id'),
		'user'=>array(self::BELONGS_TO, 'UserGroupsUser', 'user_id'),
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
			'type' => 'Type',
			'filename' => 'Filename',
			'ordering' => 'Ordering',
		);
	}
	
	
	public function BeforeDelete(){
				if (is_file($_SERVER['DOCUMENT_ROOT'].$this->original)) unlink($_SERVER['DOCUMENT_ROOT'].$this->original);
				if (is_file($_SERVER['DOCUMENT_ROOT'].$this->medium)) unlink($_SERVER['DOCUMENT_ROOT'].$this->medium);
				if (is_file($_SERVER['DOCUMENT_ROOT'].$this->small)) unlink($_SERVER['DOCUMENT_ROOT'].$this->small);		
	
				return true;
	}
	
	public function afterDelete(){
		$pictdir=$_SERVER['DOCUMENT_ROOT'].'/upload/st1234/';
		if (!count ($this->findAll('hole_id='.$this->hole_id))){
			//rmdir($pictdir.'original/'.$this->hole_id);
			//rmdir($pictdir.'medium/'.$this->hole_id);
			//rmdir($pictdir.'small/'.$this->hole_id);
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('filename',$this->filename,true);
		$criteria->compare('ordering',$this->ordering);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}