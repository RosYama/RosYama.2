<?php
/**
 * YandexOpenIDService class file.
 *
 * @author Maxim Zemskov <nodge@yandex.ru>
 * @link http://code.google.com/p/yii-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

require_once dirname(dirname(__FILE__)).'/EOpenIDService.php';

/**
 * Yandex provider class.
 * @package application.extensions.eauth.services
 */
class LJOpenIDService extends EOpenIDService {
	
	protected $name = 'livejournal';
	protected $title = 'Livejournal';
	protected $type = 'OpenID';
	protected $jsArguments = array('popup' => array('width' => 900, 'height' => 550),'autologin'=>false);
	protected $login_id;	
	protected $autologin=false;	

	protected $url='';

	protected function seturl(){
		$this->url='http://'.Yii::app()->request->getQuery('openid_identity_livejournal').'.livejournal.com'; 
	}
	
	public function __construct() {		
		$this->seturl();
	}
	
	protected $requiredAttributes = array(
		//'name' => array('first', 'namePerson/first'),
		//'lastname' => array('lastname', 'namePerson/last'),
		//'email' => array('email', 'contact/email'),
		//'url' => array('url', 'url'),
	);
	
	protected function fetchAttributes() {
		//print_r($this->attributes); die();
		if (isset($this->attributes['username']) && !empty($this->attributes['username']))
			$this->attributes['url'] = $this->attributes['username'].'.livejournal.com';
			
		if (isset($this->attributes['birthDate']) && !empty($this->attributes['birthDate']))
			$this->attributes['birthDate'] = strtotime($this->attributes['birthDate']);
		$id=strtolower($this->attributes['id']);
		$id=substr($id, 0,strlen($id)-1);
		$this->attributes['id']=$id;
		$this->attributes['name']=Yii::app()->request->getQuery('openid_identity_livejournal');
		$this->attributes['external_auth_id']='OPENID#http://www.livejournal.com/openid/server.bml';		
		
	}
}