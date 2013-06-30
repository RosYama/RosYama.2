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
	public $sendToGibddru=false;
	public $html;
	public $pdf;
	public $gibdd;
	public $gibdd_reply;
	public $application_data;
	public $holes=Array();
	public $printAllPictures=true;
	public $showDescriptions=true;
	public $textonly=false;
	
	public $requestBodyArr=Array();


	public function rules()
	{
		return array(
			// username and password are required
			//array('username, password', 'required'),
			// rememberMe needs to be a boolean
			array('html, pdf, printAllPictures, showDescriptions, textonly, sendToGibddru', 'boolean'),
			// password needs to be authenticated
			array('form_type, to, from, postaddress, address, comment, signature, application_data, gibdd, gibdd_reply', 'length'),
			array('holes', 'safe'),
		);
	}
	
	public function RequestGibddru($pdfBinary, $gibdd)
	{	
					
		$ch = curl_init('http://www.gibdd.ru/letter/');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			//'photo' => '@' . $_SERVER['DOCUMENT_ROOT'].$model->PictureFolder.'original/'.$model->picture
		)); 
 
		if (($leter = curl_exec($ch)) === false) {
			throw new Exception(curl_error($ch));
		} 
 
		curl_close($ch);	
		
		preg_match('/<input type="hidden" name="captcha_sid" value="(.*?)".*<input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" \/>/ism', $leter, $maches); 
		$captcha=str_replace('/bitrix/tools/', 'http://www.gibdd.ru/bitrix/tools/', $maches[1]);				
		
		$folder=Yii::app()->user->uploadDir.'/';
		$filename='zayavlenie_'.date('d-m-Y').'.pdf';
		file_put_contents($folder.$filename, $pdfBinary);
		$user=Yii::app()->user->userModel;
		$hole=isset($this->holes[0]) ? $this->holes[0] : new Holes;
		$model=new GibddRuForm;
		$model->captcha_sid=$captcha;
		$model->attributes=Array(
			'form_text_31'=>$gibdd,
			'form_file_27'=>str_replace($_SERVER['DOCUMENT_ROOT'], '', $folder).$filename,
			'form_text_17'=>$this->postaddress,
			'form_email_18'=>$user->email,
			'form_text_11'=>$user->last_name,
			'form_text_12'=>$user->name,
			'form_text_13'=>$user->second_name,
			'form_text_31'=>$hole->subject->region_num,
			'form_text_15'=>$hole->subject->name_full,
			'form_dropdown_SUBJECT'=>25,//заявление
			
		);
		$model->form_file_27=str_replace($_SERVER['DOCUMENT_ROOT'], '', $folder).$filename;
		if ($this->requestBodyArr){
			//form_textarea_26
			$str='';
			if (isset($this->requestBodyArr['body0'])) $str.=$this->requestBodyArr['body0'];
			if (isset($this->requestBodyArr['body1'])) $str.="\n".$this->requestBodyArr['body1'];
			if (isset($this->requestBodyArr['footerUP0'])) $str.="\n".$this->requestBodyArr['footerUP0'];
			if (isset($this->requestBodyArr['count']))
				foreach($this->requestBodyArr['count'] as $count)
					$str.="\n".$count;
			$model->form_textarea_26=$str;
		}
		return $model;
		
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
			'sendToGibddru'=>Yii::t('holes_view', 'HOLE_REQUEST_FORM_SEND_TO_GIBDDRU'),
		);
	}

}
