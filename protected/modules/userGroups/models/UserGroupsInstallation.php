<?php
/**
 * @author Nicola Puddu
 * @package userGroups
 * form model used on installation
 */
class UserGroupsInstallation extends CFormModel
{
	/**
	 * Current module version
	 * @var string
	 */
	const VERSION = '1.8';
	/**
	 * access code to access installation
	 * @var string
	 */
	public $accesscode;
	/**
	 * root username
	 * @var string
	 */
	public $root_user;
	/**
	 * root password
	 * @var string
	 */
	public $root_password;
	/**
	 * root password confirmation field
	 * @var string
	 */
	public $root_password_confirm;
	/**
	 * root email address
	 * @var string
	 */
	public $root_email;
	/**
	 * root password recovery question
	 * @var string
	 */
	public $root_question;
	/**
	 * root password recovery answer
	 * @var string
	 */
	public $root_answer;

	public function rules()
	{
		return array(
			array('accesscode', 'required', 'on'=>'access_code', 'message' => Yii::t('userGroupsModule.install', 'the Access Code cannot be blank')),
			array('accesscode', 'authenticate', 'on'=>'access_code'),
			array('root_user, root_password, root_password_confirm, root_email, root_question, root_answer', 'required', 'on'=>'installation'),
			array('root_password', 'passwordStrength', 'on'=>'installation'),
			array('root_email', 'email', 'on'=>'installation'),
			array('root_password_confirm', 'compare', 'compareAttribute' => 'root_password', 'on'=>'installation',
				'message' => Yii::t('userGroupsModule.install', 'the confirmation password doesn\'t match the password')),
		);
	}

	/**
	 * Authenticates the accesscode against the one in the configuration file.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if (!Yii::app()->controller->module->accessCode)
			$this->addError('accesscode', Yii::t('userGroupsModule.install', 'You didn\'t set the access code in your config file.'));
		if(Yii::app()->controller->module->accessCode !== $this->accesscode)
			$this->addError('accesscode', Yii::t('userGroupsModule.install', 'The two access codes don\'t match.'));
		if(!Yii::app()->getComponent('user') instanceof WebUserGroups)
			$this->addError('accesscode', Yii::t('userGroupsModule.install', 'You didn\'t correctly set the class property in the user component.<br/>Value set: <b>{class}</b><br/>Value expected: <b>WebUserGroups</b>', array('{class}'=>get_class(Yii::app()->getComponent('user'))) ));
		if(!Yii::app()->params->adminEmail)
			$this->addError('accesscode', Yii::t('userGroupsModule.install', 'You didn\'t set an adminEmail param inside your application configuration file.'));
	}

	/**
	 * Authenticates the password against checking it's strength.
	 * This is the 'passwordStrength' validator as declared in rules().
	 */
	public function passwordStrength($attribute,$params)
	{
		if(!preg_match('/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/', $this->root_password))
			$this->addError('root_password',Yii::t('userGroupsModule.install', 'for security reasons the root password must contain at least 2 digits and 2 letters'));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'access_code' => Yii::t('userGroupsModule.install', 'Access Code'),
			'root_user' => Yii::t('userGroupsModule.install', 'Root User'),
			'root_password' => Yii::t('userGroupsModule.install', 'Root Password'),
			'root_password_confirm' => Yii::t('userGroupsModule.install', 'Retype Root Password'),
			'root_email' => Yii::t('userGroupsModule.install', 'Root Email'),
			'root_question' => Yii::t('userGroupsModule.general', 'Password Reset: Question'),
			'root_answer' => Yii::t('userGroupsModule.general', 'Password Reset: Answer'),
		);
	}

}
