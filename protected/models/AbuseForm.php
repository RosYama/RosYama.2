<?php

class AbuseForm extends CFormModel
{
	public $text;
	public $hole_id;
	public $user_id;
	public $user_email;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('text, hole_id, user_id', 'required'),
			array('user_email', 'required', 'message'=>'В вашем профиле не заполнен адрес e-mail. Пожалуйста заполните это поле на '.CHtml::link('этой', '/profile/update').' странице и повторите попытку.'),
			array('hole_id, user_id', 'numerical', 'integerOnly'=>true),
		);
	}
	
	public function beforeValidate(){
		parent::beforeValidate();
		$this->user_email=Yii::app()->user->email;
		return true;		
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'text'=>'Описание проблемы'
		);
	}
}