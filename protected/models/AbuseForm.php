<?php

class AbuseForm extends CFormModel
{
	public $text;
	public $hole_id;
	public $user_id;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('text, hole_id, user_id', 'required'),
			array('hole_id, user_id', 'numerical', 'integerOnly'=>true),
		);
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