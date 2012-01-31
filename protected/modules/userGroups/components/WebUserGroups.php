<?php

/**
 * extends CWebUser to change the login url and give other properties to the user object
 * now every user has a group, a groupName, accessRules and a home
 * @author Nicola Puddu
 * @package userGroups
 *
 */
class WebUserGroups extends CWebUser
{
	/**
	 * @var array containing the url of the login page
	 */
	public $loginUrl = array('/userGroups/');

	/**
	 * updates the identity of the user
	 * @param string $id
	 * @param string $name
	 * @param int $group
	 * @param string $groupName
	 * @param int $level
	 * @param array $accessRules
	 * @param string $home
	 * @param bool $recovery
	 * @param mixed $states
	 * @see CWebUser::changeIdentity()
	 */
	protected function UGChangeIdentity($id,$name, $groups, $groupName, $level, $accessRules,$home,$recovery,$profile,$states)
	{
	    $this->setId($id);
	    $this->setName($name);
	    $this->setGroup($groups);
	    $this->setGroupName($groupName);
	    $this->setLevel($level);
	    $this->setAccessRules($accessRules);
	    $this->returnUrl = Yii::app()->baseUrl . ($home === NULL ? '/userGroups' : $home);
	    $this->setRecovery($recovery);
	    $this->setProfile($profile);
	    $this->loadIdentityStates($states);
	}


	protected function profileLoad()
	{

	}


	/**
	 * perform the login action
	 * @param CUserIdentity $identity
	 * @param int $duration
	 * @see CWebUser::login()
	 */
	public function login($identity,$duration=0)
	{
	    $id=$identity->getId();
	    $states=$identity->getPersistentStates();	    
	    if($this->beforeLogin($id,$states,false))
	    {	    	
	        $this->UGChangeIdentity($id,$identity->getName(), $identity->getGroup(), $identity->getGroupName(), $identity->getLevel(),
	        	$identity->getAccessRules(), $identity->getHome(), $identity->getRecovery(), $identity->getProfile(), $states);

	        $this->profileLoad();
	        if($duration>0)
	        {
	            if($this->allowAutoLogin)
	                $this->saveToCookie($duration);
	            else
	                throw new CException(Yii::t('userGroupsModule.admin','{class}.allowAutoLogin must be set true in order to use cookie-based authentication.',
	                    array('{class}'=>get_class($this))));
	        }

	        $this->afterLogin(false);
	    }
	}


	/**
	 * save the user identity in a cookie
	 * override of the saveToCookie method
	 * @see CWebUser::saveToCookie()
	 */
	protected function saveToCookie($duration)
	{
		$app=Yii::app();
		$cookie=$this->createIdentityCookie($this->getStateKeyPrefix());
		$cookie->expire=time()+$duration;
		$data=array(
			$this->getId(),
			$this->getName(),
			$this->getGroup(),
			$this->getGroupName(),
			$this->getLevel(),
			$this->getAccessRules(),
			NULL, // home value
			$this->getRecovery(),
			$this->getProfile(),
			$duration,
			$this->saveIdentityStates(),
		);
		$cookie->value=$app->getSecurityManager()->hashData(serialize($data));
		$app->getRequest()->getCookies()->add($cookie->name,$cookie);
	}

	/**
	 * restore user from cookie
	 * override of the restoreFromCookie method
	 * @see CWebUser::restoreFromCookie()
	 */
	protected function restoreFromCookie()
	{
		$app=Yii::app();
		$cookie=$app->getRequest()->getCookies()->itemAt($this->getStateKeyPrefix());
		if($cookie && !empty($cookie->value) && ($data=$app->getSecurityManager()->validateData($cookie->value))!==false)
		{
			$data=@unserialize($data);
			if(is_array($data) && isset($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[8],$data[9], $data[10]))
			{
				list($id,$name, $group, $groupName, $level, $accessRules, $home, $recovery, $profile,$duration,$states)=$data;
				if($this->beforeLogin($id,$states,true))
				{
					$this->UGChangeIdentity($id,$name, $group, $groupName, $level,
	        			$accessRules, $home, $recovery, $profile, $states);
					if($this->autoRenewCookie)
					{
						$cookie->expire=time()+$duration;
						$app->getRequest()->getCookies()->add($cookie->name,$cookie);
					}
					$this->afterLogin(true);
				}
			}
		}
	}


	/**
	 * accessRules setter
	 * @param array $value
	 */
	public function setAccessRules($value)
	{
		$this->setState('__accessRules',$value);
	}

	/**
	 * accessRules getter
	 * @return array
	 */
	public function getAccessRules()
	{
		return $this->getState('__accessRules');
	}

	/**
	 * group setter
	 * @param int $value
	 */
	public function setGroup($value)
	{
		$this->setState('__group',$value);
	}

	/**
	 * group getter
	 * @return int
	 */
	public function getGroup()
	{
		return $this->getState('__group');
	}

	/**
	 * groupName setter
	 * @param string $value
	 */
	public function setGroupName($value)
	{
		$this->setState('__groupName',$value);
	}

	/**
	 * groupName getter
	 * @return string
	 */
	public function getGroupName()
	{
		return $this->getState('__groupName');
	}

	/**
	 * user group level setter
	 * @param int $value
	 */
	public function setLevel($value)
	{
		$this->setState('__level',$value);
	}

	/**
	 * user group level getter
	 * @return int
	 */
	public function getLevel()
	{
		return $this->getState('__level');
	}

	/**
	 * recovery setter
	 * @param bool $value
	 */
	public function setRecovery($value)
	{
		$this->setState('__recovery',$value);
	}

	/**
	 * recovery getter
	 * @return bool $value
	 */
	public function getRecovery()
	{
		return $this->getState('__recovery');
	}

	/**
	 * profile extension's data setter
	 * @param mixed $value
	 * @since 1.7
	 */
	public function setProfile($value)
	{
		$this->setState('__profile', $value);
	}

	/**
	 * profile extension's data getter
	 * @return array
	 * @since 1.7
	 */
	public function getProfile()
	{
		return $this->getState('__profile');
	}

	public function getEmail()
	{
		return UserGroupsUser::model()->findByPk((int)Yii::app()->user->id)->email;
	}
	
	public function getUserModel()
	{
		return UserGroupsUser::model()->findByPk((int)Yii::app()->user->id);
	}	

	/**
	 * profile extension's attribute getter
	 * @param string $profileModel
	 * @param string $attribute
	 * @since 1.7
	 */
	public function profile($profileModel, $attribute)
	{
		$profile = $this->profile;

		if (isset($profile[$profileModel][$attribute]))
			return $profile[$profileModel][$attribute];
		else
			return NULL;
	}

	/**
	 * action performed when to access a specific page the login is required
	 */
	public function loginRequired()
	{
	    $app=Yii::app();
	    $request=$app->getRequest();

	    if(!$request->getIsAjaxRequest())
	        $this->setReturnUrl($request->getUrl());

	    if(($url=$this->loginUrl)!==null)
	    {
	        if(is_array($url))
	        {
	            $route=isset($url[0]) ? $url[0] : $app->defaultController;
	            $url=$app->createUrl($route,array_splice($url,1));
	        }

	        if ($request->getIsAjaxRequest())
	        	$url .= '?_isAjax=1';

	        $request->redirect($url);
	    }
	    else
	        throw new CHttpException(403,Yii::t('userGroupsModule.general','Login Required'));
	}

	/**
	 * check the permissions of the user and return a Boolean
	 * to indicate whether or not he has access to the page
	 * @param mixed $permission
	 * @param string $configuration optional additional configuration to check
	 * @param string $op operator to use when checking a configuration
	 * @return bool
	 */
	public function pbac($req_permission, $configuration = false, $op = 'AND')
	{
		// import needed models
		Yii::import('userGroups.models.UserGroupsUser');
		Yii::import('userGroups.models.UserGroupsConfiguration');
		// grants access to root and to super admins according to configuration
		if (Yii::app()->user->accessRules === UserGroupsUser::ROOT_ACCESS)
			return true;
		elseif (UserGroupsConfiguration::findRule('super_admin') && isset(Yii::app()->user->accessRules['userGroups']['admin']['admin']))
			return true;

		// extract the current controller name
		$current_controller = Yii::app()->getController()->id;

		// extract the current module name
		$current_module = Yii::app()->controller->module ? Yii::app()->controller->module->id : 'Basic';

		if (is_array($req_permission)) {
			foreach ($req_permission as $p) {
				if ($this->returnPermissionResult($p, $current_module, $current_controller))
					$valid_permission = true;
			}
		}else{
			if ($this->returnPermissionResult($req_permission, $current_module, $current_controller))
				$valid_permission = true;
		}

		// set the default value of conf_check
		$conf_check = false;

		// check if a configuration was requested and checks its value
		if ($configuration) {
			$conf_check = UserGroupsConfiguration::findRule($configuration);
			// if an OR operator was used and the configuration value is positive return true
			if ($op === 'OR' && $conf_check)
				return true;
		}

		// if a configuration was requested with an AND operator and it's value was false return false
		if (!$configuration || $conf_check || $op === 'OR') {
			if (isset($valid_permission))
				return true;
			else
				return false;
		} else
			return false;
	}


	private function returnPermissionResult($request, $current_module, $current_controller)
	{
		$r = explode('.',$request);
		switch (count($r)) {
			case 1:
				$module = $current_module;
				$controller = $current_controller;
				$permission = $r[0];
				break;
			case 2:
				$module = $current_module;
				$controller = $r[0];
				$permission = $r[1];
				break;
			case 3:
				$module = $r[0];
				$controller = $r[1];
				$permission = $r[2];
				break;
		}
		// check the permissions
		if (isset(Yii::app()->user->accessRules[$module][$controller][$permission]))
			return true;
		elseif (UserGroupsConfiguration::findRule('permission_cascade')) {
			if ($permission === 'read') {
				if (isset(Yii::app()->user->accessRules[$module][$controller]['write']))
					return true;
			}
			if ($permission === 'read' || $permission === 'write') {
				if (isset(Yii::app()->user->accessRules[$module][$controller]['admin']))
					return true;
			}
		} else
			return false;
	}
	
	public function getFullname()
	{
		$str='';
		if ($this->userModel->name || $this->userModel->last_name) {
			if ($this->userModel->name) $str=$this->userModel->name;
			if ($this->userModel->last_name) $str.=' '.$this->userModel->last_name;
			}
		else $str=$this->name;
		return $str;
	}
	
	public function getIsAdmin()
	{
		if ($this->isGuest) return false;
		if ($this->GroupName=='root' || $this->GroupName=='admin') return true;
		else return false;
	}	

	public function getIsModer()
	{
		if ($this->isGuest) return false;
		if ($this->GroupName=='root' || $this->GroupName=='admin'|| $this->GroupName=='moder') return true;
		else return false;
	}
}