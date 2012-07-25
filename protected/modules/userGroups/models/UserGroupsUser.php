<?php

/**
 * @author Nicola Puddu
 * @package userGroups
 * This is the model class for table "userGroups_user".
 *
 * The followings are the available columns in table 'userGroups_user':
 * @property string $id
 * @property string $group_id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $home
 * @property string $status
 * @property string $question
 * @property string $answer
 * @property string $creation_date
 * @property string $activation_code
 * @property string $activation_time
 * @property string $last_login
 * @property string $ban
 *
 * The followings are the available model relations:
 * @property UsergroupsGroup $group
 */
class UserGroupsUser extends CActiveRecord
{
	/**
	 * contains the access permission's array of the user.
	 * may also contain the ROOT_ACCESS constant value
	 * @var mixed
	 */
	public $access;
	/**
	 * contains the value of it's groups level
	 * @var int
	 */
	public $level;
	/**
	 * group name, used just in grid views for filtering purpose
	 * @var string
	 */
	public $group_name;
	/**
	 * group home
	 * @var string
	 */
	public $group_home;
	/**
	 * captcha used on registration
	 * @var string
	 */
	public $captcha;
	/**
	 * home of the user, in a user friendly readable way
	 * @var string
	 */
	public $readable_home;
	/**
	 * old password property. Used when changing password.
	 * @var string
	 */
	public $old_password;
	/**
	 * password confirm property
	 * @var string
	 */
	public $password_confirm;
	/**
	 * these attributes are for the login action
	 * @var string
	 */
	public $rememberMe;
	private $_identity;
	/**
	 * this constant rappresent the root id
	 * @var int
	 */
	const ROOT = 1;
	/**
	 * this constant rappresent the root access permissions
	 * @var string
	 */
	const ROOT_ACCESS = 'ALL';
	/**
	 * this constant rappresent the root level
	 * @var int
	 */
	const ROOT_LEVEL = 100;
	/**
	 * these constants are for user status
	 * @var int
	 */
	const BANNED = 0;
	const WAITING_ACTIVATION = 1;
	const WAITING_APPROVAL = 2;
	const PASSWORD_CHANGE_REQUEST = 3;
	const ACTIVE = 4;
	/**
	 * these constats rappresent the possible views
	 * and must be used in other models when extending
	 * the user profile
	 * @var string
	 */
	const VIEW = 'view';
	const EDIT = 'edit';
	const REGISTRATION = 'registration';
	
	public $notUseAfrefind=false;

	/**
	 * Returns the static model of the specified AR class.
	 * @return UserGroupsUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return Yii::app()->db->tablePrefix.'usergroups_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// load validation rules folder
		Yii::import('userGroups.validation.*');
		// rules
		$rules = array(
			array('group_id', 'length', 'max'=>20),
			array('username, password, home, last_name,second_name, name', 'length', 'max'=>120),
			array('xml_id, external_auth_id', 'length', 'max'=>255),
			array('email', 'email'),
			array('is_bitrix_pass', 'numerical', 'integerOnly'=>true),
			array('rememberMe, params', 'safe'),
			// rules for registration
			array('captcha', 'required', 'on' => 'registration'),
			array('captcha', 'captcha', 'on' => 'registration'),
			// rules for activation
			array('username, activation_code','required','on'=>'activate'),
			array('activation_code','checkCode','on'=>'activate'),
			// rules for passRequest
			array('username, email','required','on'=>'passRequest'),
			array('email', 'checkMail', 'on'=>'passRequest'),
			array('answer', 'securityQuestion', 'on'=>'passRequest'),
			// rules for mailRequest
			array('mail','requestableMail','on'=>'mailRequest'),
			// rules for changePassword
			array('old_password', 'required', 'on' =>'changePassword'),
			array('old_password', 'oldPassMatch', 'on' =>'changePassword'),
			// rules for admin
			array('group', 'levelCheck', 'on' => 'admin'),
			// rules for multiple scenarios
			array('username, password', 'required', 'on' => array('login', 'registration')),
			array('email, old_password, password, password_confirm', 'accountOwnership', 'on'=>array('changeMisc', 'changePassword')),
			array('username', 'length', 'min'=>4, 'on'=>array('changePassword')),
			array('email', 'required', 'on'=>array('registration','admin','mailRequest','changeMisc','invitation')),
			array('username, email', 'unique', 'on'=>array('registration','admin', 'recovery','changeMisc', 'invitation')),
			array('username', 'match', 'pattern'=>'/^[A-Za-z0-9-_\-]{4,}$/', 'on'=>array('registration','admin','recovery', 'changePassword'),
				'message' => 'Имя пользователя может состоять из латинских букв и символов "-" и "_"'),
			array('password', 'required', 'on'=>array('recovery','changePassword')),
			array('password', 'passwordStrength', 'on'=>array('registration','admin','recovery','changePassword')),
			array('password_confirm', 'required', 'on'=>array('registration', 'recovery','changePassword')),
			array('password_confirm', 'compare', 'compareAttribute' => 'password','on'=>array('changePassword','recovery', 'registration'),
				'message' => 'Пароли не совпадают'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, group_name, group_id, username, home, status, name, second_name, last_name', 'safe', 'on'=>'search'),
		);

		if (UserGroupsConfiguration::findRule('simple_password_reset') === false)
			array_push($rules, array('question, answer', 'required', 'on'=>array('recovery', 'registration', 'changePassword')));

		return $rules;
	}

	/**
	 * check if the group assigned to the user has a lower
	 * level then the one of the user who is creating or
	 * updating the user
	 * This is the 'levelCheck' validator as declared in rules().
	 */
	public function levelCheck($attribute,$params)
	{
		$group = UserGroupsGroup::model()->findByPk((int)$this->group_id);
		if ($group->level >= Yii::app()->user->level)
			$this->addError('level', Yii::t('UserGroupsModule.admin','You cannot assign to a User a Group that has a Level equal or higher then the one you belong to'));
	}

	/**
	 * check if the activation code is valid
	 * This is the 'checkCode' validator as declared in rules().
	 */
	public function checkCode($attribute,$params)
	{
		$user = self::model()->findByAttributes(array('username'=>$this->username));
		if (empty($user))
			$this->addError('username', Yii::t('UserGroupsModule.recovery','Username not valid'));
		else if ((int)$user->status !== self::WAITING_ACTIVATION && (int)$user->status !== self::PASSWORD_CHANGE_REQUEST && (int)$user->status !== self::ACTIVE)
			$this->addError('username', Yii::t('UserGroupsModule.recovery','Username not valid'));
		else if ($user->activation_code !== $this->activation_code)
			$this->addError('activation_code', Yii::t('UserGroupsModule.recovery','Invalid activation code'));
	}

	/**
	 * check if the email belongs to the user
	 * This is the 'checkMail' validator as declared in rules().
	 */
	public function checkMail($attribute, $params)
	{
		$user = self::model()->findByAttributes(array('username'=>$this->username));
		if (empty($user))
			$this->addError('username', Yii::t('UserGroupsModule.recovery','Username not valid'));
		else if ((int)$user->status !== self::ACTIVE)
			$this->addError('username', Yii::t('UserGroupsModule.recovery','Username not valid'));
		else if ($user->email !== $this->email)
			$this->addError('email', Yii::t('UserGroupsModule.recovery','Invalid email address'));
	}

	/**
	 * check the answer to the security question
	 * This is the 'securityQuestion' validator as declared in rules().
	 */
	public function securityQuestion($attribute, $params)
	{
		if (UserGroupsConfiguration::findRule('simple_password_reset'))
			return true;
		$user = self::model()->findByAttributes(array('username'=>$this->username));
		if (empty($user))
			$this->addError('username', Yii::t('UserGroupsModule.recovery','Username not valid'));
		else if ((int)$user->status !== self::ACTIVE)
			$this->addError('username', Yii::t('UserGroupsModule.recovery','Username not valid'));
		else if ($user->answer !== $this->answer) {
			$this->addError('question', $user->question);
			$this->addError('answer', Yii::t('UserGroupsModule.recovery','Input the right answer'));
		}
	}

	/**
	 * check if a mail may be sent to the user corresponding to the
	 * given email address
	 * This is the 'requestableMail' validator as declared in rules().
	 */
	public function requestableMail($attribute,$params)
	{

		$user = self::model()->findByAttributes(array('email'=>$this->email));
		if (empty($user))
			$this->addError('email', Yii::t('UserGroupsModule.general','Invalid email address'));
		else if ((int)$user->status !== self::WAITING_ACTIVATION)
			$this->addError('email', Yii::t('UserGroupsModule.general','Invalid email address'));
	}

	/**
	 * check if a mail may be sent to the user corresponding to the
	 * given email address
	 * This is the 'oldPassMatch' validator as declared in rules().
	 */
	public function oldPassMatch($attribute,$params)
	{		
		// check if you have user admin permission, in that case this validation will
		// be skipped, otherwise will check if you are trying to update your own account
		
		if ((Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin')) && Yii::app()->user->id !== $this->id)
			return true;
		// load the user model and check if the old password match
		$user = self::model()->findByPk($this->id);
		
		if (!$user->password) return true;
		
		if ($user->is_bitrix_pass){
			if(strlen($user->password) > 32)
					{
						$salt = substr($user->password, 0, strlen($user->password) - 32);
						$db_password = substr($user->password, -32);
					}
					else
					{
						$salt = "";
						$db_password = $user->password;
					}
			$user_password =  md5($salt.$this->old_password);
			//echo $salt.'<br/>'.$user_password.'<br/>'.$db_password;
			//die();
		}
		else {
			$user_password=md5($this->old_password . $user->getSalt());
			$db_password = $user->password;
		}		
		
		if ($db_password !== $user_password)
			$this->addError('old_password', Yii::t('UserGroupsModule.general','You didn\'t enter the correct password'));
	}

	/**
	 * check if you own the user account you are about to update
	 * This is the 'accountOwnership' validator as declared in rules().
	 */
	public function accountOwnership($attribute,$params)
	{
		// check if you have user admin permission, in that case this validation will
		// be skipped, otherwise will check if you own the account
		if (Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin'))
			return true;
		else if ($this->id !== Yii::app()->user->id)
			$this->addError($attribute, Yii::t('UserGroupsModule.general','You are not allowed to update other accounts'));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		Yii::import('userGroups.models.UserGroupsGroup');
		Yii::import('userGroups.models.UserGroupsAccess');
		// define basic relation with groups
		$relations = array(
			'relUserGroupsGroup' => array(self::BELONGS_TO, 'UserGroupsGroup', 'group_id'),
			'holes' => array(self::HAS_MANY, 'Holes', 'USER_ID'),
			'holes_cnt' => array(self::STAT, 'Holes', 'USER_ID'),
			'holes_fixed_cnt' => array(self::STAT, 'Holes', 'USER_ID', 'condition'=>'STATE="fixed"'),
			'holes_fresh_cnt' => array(self::STAT, 'Holes', 'USER_ID', 'condition'=>'STATE="fresh"'),
			'hole_area'=> array(self::HAS_MANY, 'UserAreaShapes', 'ug_id', 'with'=>'points'),
			'requests'=>array(self::HAS_MANY, 'HoleRequests', 'user_id'),
			'selected_holes_lists'=> array(self::HAS_MANY, 'UserSelectedLists', 'user_id', 'order'=>'selected_holes_lists.date_created desc'),
			);
		// extract profile models list
		$modulesData = Yii::app()->getModules();
		$profiles = isset($modulesData['userGroups']['profile']) ? $modulesData['userGroups']['profile'] : array();
		// makes the relations
		foreach ($profiles as $p) {
			$relations['rel'.$p] = array(self::HAS_ONE, $p, 'ug_id');
		}

		return $relations;
	}
	
	protected function beforeDelete()
	{
		parent::beforeDelete();
		foreach ($this->holes as $item) $item->delete();
		foreach ($this->hole_area as $item) $item->delete();
		foreach ($this->selected_holes_lists as $item) $item->delete();		
		return true;
	}	
	
	public function getAreaNeighbors()
	{
		if (!$this->hole_area) return Array();
		$condition='shape_id IN ('.implode(',',CHtml::listData($this->hole_area,'id','id')).')';
		//print_r(CHtml::listData($this->hole_area,'id','id'));
		$left=UserAreaShapePoints::model()->find(Array('condition'=>$condition, 'order'=>'lat ASC'))->lat;
		$right=UserAreaShapePoints::model()->find(Array('condition'=>$condition, 'order'=>'lat DESC'))->lat;
		$top=UserAreaShapePoints::model()->find(Array('condition'=>$condition, 'order'=>'lng DESC'))->lng;
		$bottom=UserAreaShapePoints::model()->find(Array('condition'=>$condition, 'order'=>'lng ASC'))->lng;
		
		$criteria=new CDbCriteria;
		$criteria->with='hole_area';
		$criteria->condition='points.lat >= '.($left-0.1);
		$criteria->addCondition('points.lat <= '.($right+0.1), 'AND');
		$criteria->addCondition('points.lng <= '.($top+0.1), 'AND');
		$criteria->addCondition('points.lat >= '.($bottom-0.1), 'AND');
		$criteria->addCondition('t.id != '.$this->id);
		$criteria->addCondition('t.params LIKE ("%showMyarea%") OR t.params IS NULL');
		$criteria->together=true;
		return $this->findAll($criteria);
		
		//echo $point->lat.'=>'.$point->lng.'<br/>'; 
	}	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'group_id' => 'Группа',
			'name' => 'Имя',
			'second_name'=>'Отчество',
			'last_name' => 'Фамилия',
			'username' => 'Логин',
			'password' => 'Пароль',
			'password_confirm' => 'Подтверждение пароля',
			'old_password' => 'Старый пароль',
			'email' => 'E-mail',
			'access' => 'Доступ',
			'home' => 'Домашняя страница',
			'creation_date' => 'дата создания',
			'question' => 'Вопрос',
			'answer' => 'Ответ',
			'readable_home' => 'Домашняя страница',
			'captcha' => 'Введите слово на картинке',
			'rememberMe' => 'Запомнить меня на этом компьютере',
			'params'=>'Другим пользователям :',
			'activation_code'=>'Код активации',
		);
	}
	
	public function getParamsFields()
	{
		return array(
			'showFullname' => 'Показывать имя и фамилию',
			'showAboutme' => 'Показывать информацию "обо мне"',
			'showContactForm' => 'Разрешать пользователям отправлять сообщения на e-mail',
			'showMyarea'=>'Показывать границы моего участка',
		);
	}
	
	
	public function getParam($str)
	{
		if (!$this->params || !is_array($this->params)) return true;
		foreach ($this->params as $param) if ($str==$param) return true;
		return false;
	}	
	

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria=new CDbCriteria;
		$criteria->with=array('relUserGroupsGroup');
		if (Yii::app()->db->drivername === 'pgsql') { // postgres doesn't like unquoted camelcase names
			$criteria->order='"relUserGroupsGroup".level DESC, "relUserGroupsGroup".groupname';
			$criteria->compare('"relUserGroupsGroup".groupname',$this->group_name,true);
			$criteria->compare('"relUserGroupsGroup".level <',Yii::app()->user->level -1,false);
		}else{
			$criteria->order='relUserGroupsGroup.level DESC, relUserGroupsGroup.groupname';
			$criteria->compare('relUserGroupsGroup.groupname',$this->group_name,true);
			$criteria->compare('relUserGroupsGroup.level <',Yii::app()->user->level -1,false);
		}
		$criteria->compare('id',$this->id,true);
		$criteria->compare('group_id',$this->group_id,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('second_name',$this->second_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('home',$this->home,true);
		// set the default to status active unless the person loading the view has
		// user admin rights or admin admin rights
		if (Yii::app()->user->pbac('userGroups.user.admin') || Yii::app()->user->pbac('userGroups.admin.admin'))
			$criteria->compare('status', $this->status === 'null' ? NULL : $this->status);
		else
			$criteria->compare('status', self::ACTIVE);


		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>10),
		));
	}

	/**
     * parameters additional preparations before saving the user
     */
	protected function beforeSave()
	{
		if (parent::beforeSave()) {
		$this->params=serialize($this->params);
			// set the new user creation_date
			if ($this->isNewRecord && $this->scenario != 'import')
				$this->creation_date = date('Y-m-d H:i:s');

			// populate the attributes when a new record is created in an admin scenario
			if ($this->scenario === 'admin' && $this->isNewRecord && (empty($this->password) || empty($this->username))) {
				$this->status = self::WAITING_ACTIVATION;
				$this->activation_code = uniqid();
				$this->activation_time = date('Y-m-d H:i:s');
				if (empty($this->username))
					$this->username = uniqid('_user');
			} else if (($this->scenario === 'admin' && $this->isNewRecord) || $this->scenario === 'recovery' || $this->scenario === 'swift_recovery')
				// sets the right status based on configurations
				if ((int)$this->status === self::WAITING_ACTIVATION && UserGroupsConfiguration::findRule('user_need_approval')
					&& ($this->scenario === 'recovery' || $this->scenario === 'swift_recovery'))
					$this->status = self::WAITING_APPROVAL;
				else
					$this->status = self::ACTIVE;
			// if it's a new record generates a new password if a password was defined
			if (($this->isNewRecord || $this->scenario === 'recovery' || $this->scenario === 'changePassword') && !empty($this->password) && $this->scenario != 'import') {
				$this->password = md5($this->password . $this->getSalt());
				$this->is_bitrix_pass=0;
			}
			// in the passRequest scenario change the status and delete the old password
			if ($this->scenario === 'passRequest') {		
				//$this->status = self::PASSWORD_CHANGE_REQUEST;
				//$this->password = NULL;
				//$this->is_bitrix_pass=0;
				$this->activation_code = uniqid();
				$this->activation_time = date('Y-m-d H:i:s');
			}
			// on invitations set the waiting_activation status and activation code
			if ($this->scenario === 'invitation') {
				$this->status = self::WAITING_ACTIVATION;
				$this->username = uniqid('_user');
				$this->activation_code = uniqid();
				$this->activation_time = date('Y-m-d H:i:s');
				$this->group_id = UserGroupsConfiguration::findRule('user_registration_group');
			}
			// sets the correct user status and group upon registration based on the configurations
			if ($this->scenario === 'registration') {
				$this->group_id = UserGroupsConfiguration::findRule('user_registration_group');
				if (UserGroupsConfiguration::findRule('user_need_activation')) {
					$this->status = self::WAITING_ACTIVATION;
					$this->activation_code = uniqid();
					$this->activation_time = date('Y-m-d H:i:s');
				} else if (UserGroupsConfiguration::findRule('user_need_approval'))
					$this->status = self::WAITING_APPROVAL;
				else
					$this->status = self::ACTIVE;
			}
			// erese the activation code for security reasons
			if ((int)$this->status !== self::WAITING_ACTIVATION && (int)$this->status !== self::WAITING_APPROVAL && (int)$this->status !== self::PASSWORD_CHANGE_REQUEST && $this->scenario !== 'passRequest')
				$this->activation_code = NULL;
			// sanitize the value of home
			if ($this->home === '0')
				$this->home = NULL;
			return true;
		}
		return false;
	}

	protected function afterSave()
	{
		parent::afterSave();
		// send the needed emails for account activation
		if (($this->scenario === 'admin' || $this->scenario === 'registration') && $this->status === self::WAITING_ACTIVATION) {
			$mail = new UGMail($this, UGMail::ACTIVATION);
			$mail->send();
		}

		// set the flash messages
		if ($this->scenario === 'registration' || $this->scenario === 'recovery' || $this->scenario === 'swift_recovery') {
			if ((int)$this->status === self::WAITING_ACTIVATION)
				Yii::app()->user->setFlash('success', Yii::t('UserGroupsModule.general','An email was sent with the instructions to activate your account to the address {email}.', array('{email}'=>$this->email)));
			else if ((int)$this->status === self::WAITING_APPROVAL)
				Yii::app()->user->setFlash('success', Yii::t('UserGroupsModule.general','Registration Complete. You now have to wait for an admin to approve your account.'));
			else
				Yii::app()->user->setFlash('success', Yii::t('UserGroupsModule.general','Registration Complete, you can now login.'));
		}
	}

	/**
	 * parameters preparation after a select is executed
	 */
	public function afterFind()
	{
		if (!$this->notUseAfrefind){	
			if (!$this->relProfile){
				$this->relProfile=new Profile;
				$this->relProfile->ug_id=$this->id;
				$this->relProfile->save();
			}
		
			// retrieve the group name
			$this->group_name = $this->relUserGroupsGroup->groupname;
			// retrieve the user access permission's arra
			if ((int)$this->id === self::ROOT)
				$this->access = self::ROOT_ACCESS;
			else {
				$this->access = UserGroupsAccess::findRules(UserGroupsAccess::USER, $this->id);
			}
	
			// copy the level of it's own group
			$this->level = $this->relUserGroupsGroup->level;
	
			// copy the group home
			$this->group_home = $this->relUserGroupsGroup->home;
			
			//Получение параметров
			if ($this->params) $this->params=unserialize($this->params);
			else $this->params=array_keys($this->ParamsFields);
			
			// get the user readable home
			$home_array = UserGroupsAccess::homeList();
			if ($this->home)
				$this->readable_home = isset($home_array[$this->home]) ? $home_array[$this->home] : $this->home;
			else
				$this->readable_home = isset($home_array[$this->group_home]) ? $home_array[$this->group_home].' - <i><b>Inherited from Group</b></i>' : $this->group_home;
		}
		parent::afterFind();
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login($mode = 'regular')
	{
		if($this->_identity===null)
		{
			if ($mode === 'regular') {
				$this->_identity=new UserGroupsIdentity($this->username,$this->password);
				$this->_identity->authenticate();
			} else if ($mode === 'fromHash') {
				$this->_identity=new UserGroupsIdentity($this->username,'',$this->password);
				$this->_identity->authenticate();
			} else if ($mode === 'recovery') {
				$this->_identity=new UserGroupsIdentity($this->username,$this->activation_code);
				$this->_identity->recovery();
			} else if ($mode === 'activate') {
				$this->_identity=new UserGroupsIdentity($this->username,$this->activation_code);
				$this->_identity->recovery('activate');
			}
			else if ($mode === 'service') {
				$service = Yii::app()->request->getQuery('service');
				if (isset($service)) {
					$authIdentity = Yii::app()->eauth->getIdentity($service);
					$authIdentity->redirectUrl = Yii::app()->user->returnUrl;
       				$authIdentity->cancelUrl = Yii::app()->getController()->createAbsoluteUrl('/UserGroups/');
       				if ($authIdentity->authenticate()) {
						$this->_identity=new ServiceUserIdentity($authIdentity);       				
						$this->_identity->authenticate();
					}
				}				
			}
		}
		if($this->_identity->errorCode===UserGroupsIdentity::ERROR_NONE)
		{			
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else if ($this->_identity->errorCode === UserGroupsIdentity::ERROR_USER_BANNED)
			$this->addError('username',Yii::t('UserGroupsModule.general','We are sorry, but your account is banned'));
		else if ($this->_identity->errorCode === UserGroupsIdentity::ERROR_USER_INACTIVE)
			$this->addError('username',Yii::t('UserGroupsModule.general','Account not active').'<br/>'.CHtml::link(Yii::t('UserGroupsModule.general','Activate the account'), array('/userGroups/user/activate')));
		else if ($this->_identity->errorCode === UserGroupsIdentity::ERROR_USER_APPROVAL)
			$this->addError('username',Yii::t('UserGroupsModule.general','This account is not approved yet'));
		else if ($this->_identity->errorCode === UserGroupsIdentity::ERROR_PASSWORD_REQUESTED)
			$this->addError('password',Yii::t('UserGroupsModule.general','A password change has been requested.<br/>You won\'t be able to login until you change the password.'));
		else if ($this->_identity->errorCode === UserGroupsIdentity::ERROR_ACTIVATION_CODE)
			$this->addError('activation_code',Yii::t('UserGroupsModule.recovery','Invalid activation code'));
		else if ($this->_identity->errorCode === UserGroupsIdentity::ERROR_USER_ACTIVE)
			$this->addError('activation_code',Yii::t('UserGroupsModule.recovery','This user cannot login in recovery mode.'));
		else
			$this->addError('password',Yii::t('UserGroupsModule.recovery','wrong user or password.').'<br/>'.CHtml::link(Yii::t('UserGroupsModule.recovery', 'Password Recovery'), array('/userGroups/user/passRequest')));
			return false;
	}

	/**
	 * @return string the user salt
	 */
	public function getSalt()
	{
		// TODO when stop supporting php 5.2 use dateTime
		// turn the creation_date into the corresponding timestamp
		list($date, $time) = explode(' ', $this->creation_date);
		$date = explode('-', $date);
		$time = explode(':', $time);

		date_default_timezone_set('UTC');
    	$timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
		// create the salt
		$salt = $this->username . $timestamp;
		// add the additional salt if it's provided
		if (isset(Yii::app()->controller->module->salt))
			$salt .= Yii::app()->controller->module->salt;
		else {
			$modulesData = Yii::app()->getModules();
			$salt .= isset($modulesData['userGroups']['salt'])?$modulesData['userGroups']['salt']:'111';
		}	

		return $salt;
	}
	
	public function getFullname()
	{
		$str='';
		if ($this->name || $this->last_name) {
			if ($this->name) $str=$this->name;
			if ($this->last_name) $str.=' '.$this->last_name;
			}
		else $str=$this->username;
		return $str;
	}	
	
	
	
}