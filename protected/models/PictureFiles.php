<?php

/**
 * This is the model class for table "ttv_pictures".
 *
 * The followings are the available columns in table 'ttv_pictures':
 * @property integer $id
 * @property string $img_type
 * @property integer $element_id
 * @property string $folder
 * @property string $filename
 * @property integer $width
 * @property integer $height
 *
 * The followings are the available model relations:
 */
class PictureFiles extends CFormModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Pictures the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('files', 'required'),
			array('files', 'file', 'types'=>'jpg, gif, png'),
		);
	}

	/**
	 * @return array relational rules.
	 */


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'files' => 'Нужно загрузить фотографии',

		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */

}	?>