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
	protected $jsArguments = array('popup' => array('width' => 900, 'height' => 550));
	protected $login_id;	
	protected $autologin=false;	

	protected $url='http://www.livejournal.com/openid/approve.bml';

	/*protected function seturl(){
		$this->url='http://'.Yii::app()->request->getQuery('openid_identity_livejournal').'.livejournal.com';
	}
	
	public function __construct() {		
		$this->seturl();
	}*/
	
	protected $requiredAttributes = array(
		'name' => array('first', 'namePerson'),
		//'lastname' => array('last', 'namePerson'),
		//'email' => array('email', 'contact/email'),
		//'gender' => array('gender', 'person/gender'),
		//'birthDate' => array('dob', 'birthDate'),  
	);
	
	protected function fetchAttributes() {
		//print_r($this->attributes); die();
		if (isset($this->attributes['username']) && !empty($this->attributes['username']))
			$this->attributes['url'] = $this->attributes['username'].'.livejournal.com';
			
		if (isset($this->attributes['birthDate']) && !empty($this->attributes['birthDate']))
			$this->attributes['birthDate'] = strtotime($this->attributes['birthDate']);
		$this->attributes['id']=strtolower($this->attributes['id']);
		$this->attributes['external_auth_id']='OPENID#http://www.livejournal.com/openid/server.bml';		
		
	}
}