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
	
	public function getResult($model, $onlyFile=false){	
	
				$_images = array();
				$date3 = $this->application_data   ? strtotime($this->application_data) : time();
				if ($this->form_type == 'prosecutor')
					$date3 = strtotime($this->application_data);
					
				$date2 = ($this->form_type == 'prosecutor' || $this->form_type == 'prosecutor2') && $model->request_gibdd ? $model->request_gibdd->date_sent  : time();
				$_data = array
				(
					'chief'       => $this->to,
					'fio'         => $this->from,
					'address'     => $this->postaddress,
					'date1.day'   => date('d', $model->DATE_CREATED ? $model->DATE_CREATED : time()),
					'date1.month' => date('m', $model->DATE_CREATED ? $model->DATE_CREATED : time()),
					'date1.year'  => date('Y', $model->DATE_CREATED ? $model->DATE_CREATED : time()),
					'street'      => $this->address,
					'date2.day'   => date('d', $date2),
					'date2.month' => date('m', $date2),
					'date2.year'  => date('Y', $date2),
					'signature'   => $this->signature,
					'reason'      => $this->comment,
					'date3.day'   => date('d', $date3),
					'date3.month' => date('m', $date3),
					'date3.year'  => date('Y', $date3),
					'gibdd'       => $this->gibdd,
					'gibdd_reply' => $this->gibdd_reply
				);
			
				if($this->html)
				{
					foreach($model->pictures_fresh as $picture)
					{
						$_images[] = $picture->original;
					}
					header('Content-Type: text/html; charset=utf8', true);
					$HT = new html1234();
					if (!$this->holes){
						$HT->models=Array($model);
						$HT->requestForm=$this;
						$HT->gethtml
						(							
							$this->form_type ? $this->form_type : $model->type,
							$_data,
							$_images
						);
					}	
					else {
						$HT->models=Holes::model()->findAllByPk($this->holes);
						$HT->requestForm=$this;
							$HT->gethtml
							(
								'gibdd',
								$_data,
								Array(),
								$this->printAllPictures
							);
						}
				}
				else
				{					
					foreach($model->pictures_fresh as $picture)
					{
						$_images[] = $_SERVER['DOCUMENT_ROOT'].$picture->original;
					}
												
					$PDF = new pdf1234();
					if (!$this->holes){
						$PDF->models=Array($model);
						$PDF->requestForm=$this;
						$pdfresult=$PDF->getpdf
						(
							$this->form_type ? $this->form_type : $model->type,
							$_data,
							$_images
						);
					}
					else {
						$PDF->models=Holes::model()->findAllByPk($this->holes);
						$PDF->requestForm=$this;
						$pdfresult=$PDF->getpdf
							(
								'gibdd',
								$_data,
								Array(),
								$this->printAllPictures
							);
					}
					
					if ($this->sendToGibddru && !$onlyFile) {	
							$gibdd=isset($PDF->models[0]) && $PDF->models[0]->subject ? $PDF->models[0]->subject->region_num : 0;
							$this->holes=Array($model);							
							return $this->RequestGibddru($pdfresult, $gibdd);
						}
					elseif($this->sendToGibddru && $onlyFile)
						return $this->savePdfFile($pdfresult);
					
				}
			
		
	}
	
	public function savePdfFile($pdfBinary)
	{	
		$folder=Yii::app()->user->uploadDir.'/';
		$filename='zayavlenie_'.date('d-m-Y').'.pdf';
		file_put_contents($folder.$filename, $pdfBinary);
		return str_replace($_SERVER['DOCUMENT_ROOT'], '', $folder).$filename;
	}
	
	public function RequestGibddru($pdfBinary, $gibdd)
	{	
					
		$ch = curl_init('http://www.gibdd.ru/letter/');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, Yii::app()->user->uploadDir.'/'."gibddru_cookie.txt");
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			//'photo' => '@' . $_SERVER['DOCUMENT_ROOT'].$model->PictureFolder.'original/'.$model->picture
		)); 
 
		if (($leter = curl_exec($ch)) === false) {
			throw new Exception(curl_error($ch));
		} 
 
		curl_close($ch);	
		
		preg_match('/<input type="hidden" name="captcha_sid" value="(.*?)".*<input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" \/>/ism', $leter, $maches); 
		$captcha=str_replace('/bitrix/tools/', 'http://www.gibdd.ru/bitrix/tools/', $maches[1]);				
		preg_match('/<input type="hidden" name="sessid" id="sessid" value="(.*?)"/ism', $leter, $maches); 		
		$sessid=str_replace('/bitrix/tools/', 'http://www.gibdd.ru/bitrix/tools/', $maches[1]);		
		
		
		$user=Yii::app()->user->userModel;
		$hole=isset($this->holes[0]) ? $this->holes[0] : new Holes;
		$model=new GibddRuForm;
		$model->captcha_sid=$captcha;
		$model->attributes=Array(
			'form_text_31'=>$gibdd,
			'form_file_27'=>$this->savePdfFile($pdfBinary),
			'form_text_17'=>$this->postaddress,
			'form_email_18'=>$user->email,
			'form_text_11'=>$user->last_name,
			'form_text_12'=>$user->name,
			'form_text_13'=>$user->second_name,
			'form_text_31'=>$hole->subject->region_num,
			'form_text_15'=>$hole->subject->name_full,
			'form_dropdown_SUBJECT'=>25,//заявление
			'sessid'=>$sessid,
			'reg'=>77,
			'holes'=>implode(',',CHtml::listData($this->holes, 'ID', 'ID')),
		);
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
