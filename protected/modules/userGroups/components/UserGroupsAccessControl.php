<?php
/**
 * UserGroupsAccessControl class file.
 *
 * @author Nicola Puddu <nicola@creationgears.com>
 * @link http://www.creationgears.com/
 * @license http://www.yiiframework.com/license/
 */

/**
 * UserGroupsAccessControl is an extension of CAccessControlFilter.
 * 
 * It has all the features of CAccessControlFilter plus the ability to set groups for access control.
 *
 * Here you can see an updated structure of the access rules:
 * <pre>
 * array(
 *   'allow',
 *   // optional, list of action IDs (case insensitive) that this rule applies to
 *   'actions'=>array('edit', 'delete'),
 *   // optional, list of controller IDs (case insensitive) that this rule applies to
 *   // This option is available since version 1.0.3.
 *   'controllers'=>array('post', 'admin/user'),
 *   // optional, list of usernames (case insensitive) that this rule applies to
 *   // Use * to represent all users, ? guest users, and @ authenticated users
 *   'users'=>array('thomas', 'kevin'),
 *   // optional, list of groups (case insensitive) that this rule applies to. 
 *   // You can use both group ids and group names.
 *   'groups'=>array('root', 'regulars', '5'),
 *   // optional, list of roles (case sensitive!) that this rule applies to.
 *   'roles'=>array('admin', 'editor'),
 *   // optional, list of IP address/patterns that this rule applies to
 *   // e.g. 127.0.0.1, 127.0.0.*
 *   'ips'=>array('127.0.0.1'),
 *   // optional, list of request types (case insensitive) that this rule applies to
 *   'verbs'=>array('GET', 'POST'),
 *   // optional, a PHP expression whose value indicates whether this rule applies
 *   // This option is available since version 1.0.3.
 *   'expression'=>'!$user->isGuest && $user->level==2',
 *   // optional, the customized error message to be displayed
 *   // This option is available since version 1.1.1.
 *   'message'=>'Access Denied.',
 * )
 * </pre>
 *
 * @author Nicola Puddu <nicola@creationgears.com>
 * @package userGroups
 */
class UserGroupsAccessControl extends CAccessControlFilter
{

	private $_rules=array();

	/**
	 * @return array list of access rules.
	 */
	public function getRules()
	{
		return $this->_rules;
	}

	/**
	 * @param array $rules list of access rules.
	 */
	public function setRules($rules)
	{
		foreach($rules as $rule)
		{
			if(is_array($rule) && isset($rule[0]))
			{
				$r=new UserGroupsAccessRule;
				$r->allow=$rule[0]==='allow';
				foreach(array_slice($rule,1) as $name=>$value)
				{
					if($name==='expression' || $name==='roles' || $name==='message' || $name==='ajax' || $name==='pbac')
						$r->$name=$value;
					else
						$r->$name=array_map('strtolower',$value);
				}
				$this->_rules[]=$r;
			}
		}
	}

	/**
	 * Denies the access of the user.
	 * This method is invoked when access check fails.
	 * @param IWebUser $user the current user
	 * @param string $message the error message to be displayed
	 * @since 1.0.5
	 */
	protected function accessDenied($user,$message)
	{
		if($user->getIsGuest())
			$user->loginRequired();
		else
			throw new CHttpException(403,$message);
	}
}


/**
 * UserGroupsAccessRule represents an access rule that is managed by {@link UserGroupsAccessControl}.
 *
 * @author Nicola Puddu <nicola@creationgears.com>
 * @package userGroups
 */
class UserGroupsAccessRule extends CAccessRule
{
	/**
	 * @var array list of user groups that this rule applies to. The comparison is case-insensitive.
	 */
	public $groups;
	/**
	 * @var array list of group levels that this rule applies to. Logic operators are valid.
	 */
	public $level;
	/**
	 * @var array list of role base access contro rules that this rule applies to.
	 */
	public $pbac;
	/**
	 * @var boolean whether or not the page has to be accessed via ajax.
	 */
	public $ajax;

	/**
	 * Checks whether the Web user is allowed to perform the specified action.
	 * @param CWebUser $user the user object
	 * @param CController $controller the controller currently being executed
	 * @param CAction $action the action to be performed
	 * @param string $ip the request IP address
	 * @param string $verb the request verb (GET, POST, etc.)
	 * @return integer 1 if the user is allowed, -1 if the user is denied, 0 if the rule does not apply to the user
	 */
	public function isUserAllowed($user,$controller,$action,$ip,$verb)
	{
		if($this->isActionMatched($action)
			&& $this->isUserMatched($user)
			&& $this->isRoleMatched($user)
			&& $this->isGroupMatched($user)
			&& $this->isLevelMatched($user)
			&& $this->isAjaxMatched()
			&& $this->isPbacMatched($user)
			&& $this->isIpMatched($ip)
			&& $this->isVerbMatched($verb)
			&& $this->isControllerMatched($controller)
			&& $this->isExpressionMatched($user))
			return $this->allow ? 1 : -1;
		else
			return 0;
	}
	
	/**
	 * @param IWebUser $user the user
	 * @return boolean whether the rule applies to the user
	 */
	protected function isUserMatched($user)
	{
		if(empty($this->users))
			return true;
		foreach($this->users as $u)
		{
			if($u==='*')
				return true;
			else if($u==='?' && $user->getIsGuest())
				return true;
			else if($u==='#' && $user->getRecovery())
				return true;
			else if ((int)$user->getId() === UserGroupsUser::ROOT && $u!=='?' && $u!=='#')
				return true;
			else if (UserGroupsConfiguration::findRule('super_admin') && isset(Yii::app()->user->accessRules['userGroups']['admin']['admin']) && $u!=='?' && $u!=='#')
				return true;
			else if($u==='@' && !$user->getIsGuest() && !$user->getRecovery())
				return true;
			else if(!strcasecmp($u,$user->getName()))
				return true;
		}
		return false;
	}
	
	/**
	 * @param IWebUser $user the user
	 * @return boolean whether the rule applies to the user
	 */
	protected function isGroupMatched($user)
	{
		if(empty($this->groups) || (int)$user->getGroup() === UserGroupsUser::ROOT)
			return true;
		elseif (UserGroupsConfiguration::findRule('super_admin') && isset(Yii::app()->user->accessRules['userGroups']['admin']['admin']))
			return true; 
		elseif ($user->getIsGuest() || $user->getRecovery())
			return false;
		foreach($this->groups as $g)
		{
			if($g==='*')
				return true;
			else if(!strcasecmp($g,$user->getGroup()) || !strcasecmp($g,$user->getGroupName()))
				return true;
		}
		return false;
	}
	
	/**
	 * @return boolean whether the page can be accessed according to the ajax rule
	 */
	protected function isAjaxMatched()
	{
		if (is_null($this->ajax))
			return true;
		else if ($this->ajax === Yii::app()->request->isAjaxRequest)
			return true;
		else
			return false;
	}
	
	/**
	 * @param IWebUser $user the user
	 * @return boolean whether the page can be accessed according to the pbac rule
	 */
	protected function isPbacMatched($user)
	{
		// extract the user accessRules
		$accessRules = $user->getAccessRules();
		// grant access right the way if root is asking for the page or no pbac is setted
		// or deny it right the way if user is guest or in recovery mode
		if ($user->getAccessRules() === UserGroupsUser::ROOT_ACCESS || empty($this->pbac))
			return true;
		elseif (UserGroupsConfiguration::findRule('super_admin') && isset(Yii::app()->user->accessRules['userGroups']['admin']['admin']))
			return true;
		elseif ($user->getIsGuest() || $user->getRecovery())
			return false;
		
		
		// extract the current controller name
		$current_controller = Yii::app()->getController()->id;
		
		// extract the current module name
		$current_module = Yii::app()->controller->module ? Yii::app()->controller->module->id : 'Basic';
		
		foreach ($this->pbac as $p) {
			$p = explode('.', $p);
			switch (count($p)) {
				case 1:
					$module = $current_module;
					$controller = $current_controller;
					$permission = $p[0];
					break;
				case 2:
					$module = $current_module;
					$controller = $p[0];
					$permission = $p[1];
					break;
				case 3:
					$module = $p[0];
					$controller = $p[1];
					$permission = $p[2];
					break;
			}
			// check the asked permission
			if (isset($accessRules[$module][$controller][$permission]))
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
			}
		}
		
		return false;
	}
	
	/**
	 * @param IWebUser $user the user
	 * @return boolean whether the page can be accessed according to the user group level
	 */
	protected function isLevelMatched($user)
	{
		if ((int)$user->getLevel() === UserGroupsUser::ROOT_LEVEL || empty($this->level))
			return true;
		else if (UserGroupsConfiguration::findRule('super_admin') && isset(Yii::app()->user->accessRules['userGroups']['admin']['admin']))
			return true;
		else if ($user->getIsGuest() || $user->getRecovery())
			return false;
		// check if all the rules have to match to grant access
		if (isset($this->level['strict']))
			$strict = true;
		foreach($this->level as $l)
		{
			if (is_numeric($l) && $l === $user->getLevel())
				$return = true;
			else if (!is_numeric($l)) {
				$comparison = $user->getLevel() . $l;
				if (eval("return $comparison;"))
					$return = true;
			 	else
					$strict_end = false;
			} else
				$strict_end = false;
			
			// if the rule is not strict and there was a match returns true
			// otherwise if the rule is strict and there's not return return false
			if (!isset($strict) && isset($return))
				return true;
			else if (isset($strict) && isset($strict_end))
				return false;
		}
		
		if (isset($return))
			return $return;
		
		return false;
	}
}
