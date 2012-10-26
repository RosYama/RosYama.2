<?php

class HoleRequestForm extends CFormModel
{
	public $form_type;
	public $to;
	public $from;
	public $postaddress;
	public $address;
	public $comment;
	public $signature;
	public $html;
	public $pdf;
	public $gibdd;
	public $gibdd_reply;
	public $application_data;
	public $holes=Array();
	public $printAllPictures=true;
	public $showDescriptions=true;
	public $textonly=false;


	public function rules()
	{
		return array(
			// username and password are required
			//array('username, password', 'required'),
			// rememberMe needs to be a boolean
			array('html, pdf, printAllPictures, showDescriptions, textonly', 'boolean'),
			// password needs to be authenticated
			array('form_type, to, from, postaddress, address, comment, signature, application_data, gibdd, gibdd_reply', 'length'),
			array('holes', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'to'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO'),
			'from'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_FROM'),
			'postaddress'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_POSTADDRESS'),
			'address'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_ADDRESS'),
			'comment'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_COMMENT'),
			'signature'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE'),
			'showDescriptions'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_SHOW_DESCRIPTIONS'),
			'printAllPictures'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_PRINT_PICTURES'),
		);
	}

}
