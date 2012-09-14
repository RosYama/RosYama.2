<?php

class DorogiMosForm extends CFormModel
{

	public $name;
	public $surname;
	public $fatherName;
	public $phoneNumber;
	public $email;
	public $notifyViaEmail=true;
	public $notifyViaSms=true;
	public $address;
	public $holeAddress;
	public $category;
	public $details;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('name, surname, email, holeAddress, details', 'required'),
			array('phoneNumber, address, fatherName, address', 'length', 'max'=>255),
			array('notifyViaEmail, notifyViaSms', 'numerical', 'integerOnly'=>true),
			array('email', 'email'),
		);
	}	
	
		
	public function getClient()
	{
		$username = 'rosyama'; 
		$password = '1qa@WS3ed'; 
		
		$wsse_header = new WsseAuthHeader($username, $password);
		$client = new SoapClient('http://80.234.91.18:8081/ws/v3/RequestV3.wsdl');
		$client->__setSoapHeaders(array($wsse_header));
		return $client;
	}
	
	public function getFunctions()
	{
		//$answer=$client->Classificator(Array('classificator'=>'ClassificatorProblemCategory'));		
		//$answer=$client->Category();
		
		var_dump($this->client->__getFunctions());			
	}	
	
	public function getCategories()
	{
		$answer=$this->client->Category();
		if ($answer) return $answer->categories->category;		
		else return Array();
	}	
	
	public function sendRequest($hole, $user)
	{
		if (!$hole->isMoscow) return false;
		$client=$this->client;
		Yii::app()->request->baseUrl=Yii::app()->request->hostInfo;			
		$pictures=Array();
		foreach ($hole->pictures_fresh as $pict)
			$pictures[]=Array(
				'name'=>$pict->filename,
				'fileType'=>$pict->mime,
				'content'=>$pict->binary,
			);

		$answer=$client->RequestNew(
			Array(
			'request'=>Array(
				'hidden'=>false,
				'informer'=>Array(
					'name'=>$this->name,
					'surname'=>$this->surname,
					'fatherName'=>$this->fatherName,
					'phoneNumber'=>$this->phoneNumber,
					'email'=>$this->email,
					'notifyViaEmail'=>$this->notifyViaEmail,
					'notifyViaSms'=>$this->notifyViaSms,
					'address'=>Array(
							'fullAddress'=>$this->address,
						),						
					),
				'category'=>Array('code'=>$hole->type->dorogimos_id),
				'details'=>$this->details,
				'address'=>Array(
					'latitude'=>$hole->LATITUDE,
					'longitude'=>$hole->LONGITUDE,
					'fullAddress'=>$this->holeAddress,
					'webLink'=>CController::createUrl('/holes/view', Array('id'=>$hole->ID)),
					),
				'pictures'=>$pictures,
				)
			)
		);
		if ($answer->successful){			
			$holeRequest=new HoleRequests;
			$holeRequest->hole_id=$hole->ID;
			$holeRequest->user_id=$user->id;
			$holeRequest->date_sent=time();
			$holeRequest->response_requestid=$answer->request->requestNumber;
			$holeRequest->type='dorogimos';
			$holeRequest->gibdd_id=0;
			if ($holeRequest->save()) return true;
			else print_r($holeRequest->errors); die();
		}
		return false;
	}	

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>'Имя',
			'surname'=>'Фамилия',
			'fatherName'=>'Отчество',
			'phoneNumber'=>'Телефон',
			'email'=>'E-mail',
			'notifyViaEmail'=>'Уведомлять по email',
			'notifyViaSms'=>'Уведомлять по SMS',
			'address'=>'Почтовый адрес',
			'category'=>'Категория обращения',
			'details'=>'Детальное описание дефекта',
			'holeAddress'=>'Адрес дефекта',
		);
	}
}