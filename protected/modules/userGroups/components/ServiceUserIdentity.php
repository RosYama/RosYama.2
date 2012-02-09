<?php
class ServiceUserIdentity extends CUserIdentity {
    const ERROR_USER_BANNED = 3;
	const ERROR_USER_INACTIVE = 4;
	const ERROR_USER_APPROVAL = 5;
	const ERROR_PASSWORD_REQUESTED = 6;
	const ERROR_USER_ACTIVE = 7;
	const ERROR_ACTIVATION_CODE = 8;
	const ERROR_NOT_AUTHENTICATED = 3;

    /**
     * @var EAuthServiceBase the authorization service instance.
     */
    protected $service;
    
    private $id;
	/**
	 * @var string $name the username
	 */
	private $name;
	/**
	 * @var string $group the group id of the user
	 */
	private $group;
	/**
	 * @var string $groupName the group name of the user
	 */
	private $groupName;
	/**
	 * @var array $access contains the user access restrictions
	 */
	private $accessRules;
	/**
	 * @var string $home contains the home of the user
	 */
	private $home;
	/**
	 * @var int $level level of the user group
	 */
	private $level;
	/**
	 * @var bool $recovery states if the user is logged in recovery mode
	 */
	private $recovery;
	/**
	 * @var array contains the profile extensions attributes stored in session
	 */
	private $profile;
	/**
	 * these constants rappresent new possible errors
	 * @var int
	 */
    /**
     * Constructor.
     * @param EAuthServiceBase $service the authorization service instance.
     */
    public function __construct($service) {
        $this->service = $service;
    }
    
    /**
     * Authenticates a user based on {@link username}.
     * This method is required by {@link IUserIdentity}.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {   
    
        if ($this->service && $this->service->isAuthenticated) {
            $this->username = $this->service->serviceName.'#'.$this->service->id;
            $this->setState('name', $this->username);
            $this->setState('service', $this->service->serviceName);           
            $this->errorCode = self::ERROR_NONE; 
            $model=UserGroupsUser::model()->findByAttributes(array('xml_id' => $this->service->id, 'external_auth_id' => $this->service->getAttribute('external_auth_id') ? $this->service->getAttribute('external_auth_id') : $this->service->serviceName));
			if (!$model){
						$model=new UserGroupsUser();
						$model->username = $this->username;
						$model->email = $this->service->getAttribute('email');
						if (!$model->email && $this->service->serviceName=='yandex') $model->email=$this->service->getAttribute('name').'@yandex.ru';
						$model->name = $this->service->getAttribute('name');
						$model->last_name = $this->service->getAttribute('lastname');
						$model->group_id = 2;
						$model->status=4;
						$model->params=array_keys($model->ParamsFields);
						$model->xml_id = $this->service->id;
						$model->external_auth_id = $this->service->getAttribute('external_auth_id') ? $this->service->getAttribute('external_auth_id') : $this->service->serviceName;
						$model->save();
			}			
			if(!$model)
				$this->errorCode=self::ERROR_USERNAME_INVALID;
			else if((int)$model->status === UserGroupsUser::WAITING_ACTIVATION)
				$this->errorCode=self::ERROR_USER_INACTIVE;		
			else if((int)$model->status === UserGroupsUser::WAITING_APPROVAL)
				$this->errorCode=self::ERROR_USER_APPROVAL;
			else if((int)$model->status === UserGroupsUser::BANNED)
				$this->errorCode=self::ERROR_USER_BANNED;
			else if((int)$model->status === UserGroupsUser::PASSWORD_CHANGE_REQUEST)
				$this->errorCode=self::ERROR_PASSWORD_REQUESTED;
			else {
				$this->errorCode=self::ERROR_NONE;
				$this->id = $model->id;
				$this->name = $model->username;
				$this->group = $model->group_id;
				$this->groupName = $model->relUserGroupsGroup->groupname;
				$this->level = $model->relUserGroupsGroup->level;
				$this->accessRules = $this->accessRulesComputation($model);
				$this->home = $model->home ? $model->home : $model->relUserGroupsGroup->home;
				$this->recovery = false;
				// load profile extension's data
				$this->profileLoad($model);
				// update the last login time
				$model->last_login = date('Y-m-d H:i:s');
				// run the cronjobs
				if (UserGroupsConfiguration::findRule('server_executed_crons') === false) {
					UGCron::init();
					UGCron::add(new UGCJGarbageCollection);
					UGCron::add(new UGCJUnban);
					foreach (Yii::app()->controller->module->crons as $c) {
						UGCron::add(new $c);
					}
					UGCron::run();
				}
				$model->save();
			}
			
        }
        else {
            $this->errorCode = self::ERROR_NOT_AUTHENTICATED;
        }  
	
		
		return !$this->errorCode;
    }
    
    private function accessRulesComputation($model)
	{
		if (is_array($model->access))
			return array_merge_recursive($model->relUserGroupsGroup->access, $model->access);
		else
			return $model->access;
	}

	/**
	 * get profile extensions attribute values that are
	 * supposed to be stored in session
	 * @param CActiveRecord $model
	 * @since 1.7
	 */
	private function profileLoad($model)
	{
		$array = array();
		foreach (Yii::app()->controller->module->profile as $p) {
			$class = new ReflectionClass($p);
			if ($class->hasMethod('profileSessionData')) {
				// TODO when stop supporting php 5.2 just initialize the model with variables
				$class = new $p;
				$relation = 'rel'.$p;
				foreach ($class->profileSessionData() as $sessionAttribute) {
					$array[$p][$sessionAttribute] = $model->$relation === NULL ? NULL : $model->$relation->$sessionAttribute;
				}
			}
		}

		// memory cleanup
		unset($class);
		unset($relation);
		$this->profile = $array;
	}

	/**
	 * returns the user id
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * return the username
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * returns the group id
	 * @return int
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * returns the group name
	 * @return string
	 */
	public function getGroupName()
	{
		return $this->groupName;
	}

	/**
	 * returns the user group level
	 * @return int
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * returns the accessRules value
	 * @return mixed
	 */
	public function getAccessRules()
	{
		return $this->accessRules;
	}

	/**
	 * returns the user home
	 * @return string
	 */
	public function getHome()
	{
		return $this->home;
	}

	/**
	 * returns the value of recovery
	 * @return bool
	 */
	public function getRecovery()
	{
		return $this->recovery;
	}

	/**
	 * returns the value of profile
	 * @return array
	 * @since 1.7
	 */
	public function getProfile()
	{
		return $this->profile;
	}
}
?>