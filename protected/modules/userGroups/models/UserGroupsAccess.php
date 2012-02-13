<?php
/**
 * @author Nicola Puddu
 * @package userGroups
 * 
 * This is the model class for table "usergroups_access".
 * 
 * this model also returns the data for the permission form
 * and also returns the data for the home dropDownList
 * 
 * The followings are the available columns in table 'usergroups_access':
 * @property string $element
 * @property string $element_id
 * @property string $module
 * @property string $controller
 * @property string $permission
 * 
 */
class UserGroupsAccess extends CActiveRecord
{
	/**
	 * contais the rules of the group
	 * @var array $_gRules
	 */
	private static $_gRules;
	/**
	 * contains the rules of the group
	 * @var string $_uRules
	 */
	private static $_uRules;
	/**
	 * array of declared classes
	 * @var array $_declaredClasses;
	 */
	private static $_declaredClasses;
	/**
	 * array of class to be skipped
	 * @var array $_alreadyIncluded
	 */
	private static $_alreadyIncluded;
	/**
	 * name of the module of the controller
	 * @var string $_moduleName
	 */
	private static $_moduleName;
	/**
	 * name of the controller
	 * @var string $_controllerName
	 */
	private static $_controllerName;
	/**
	 * array that will be used to generate the data provider
	 * @var array $_rawData
	 */
	private static $_rawData = array();
	/**
	 * these constants identify user and group
	 * @var int
	 */
	const USER = 1;
	const GROUP = 2;
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return UserGroupsAccess the static model class
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
		return Yii::app()->db->tablePrefix.'usergroups_access';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('element, element_id, module, controller, permission', 'required'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'element' => Yii::t('UserGroupsModule.admin','Element'),
			'element_id' => 'Element ID',
			'module' => Yii::t('UserGroupsModule.admin','Module'),
			'controller' => Yii::t('UserGroupsModule.admin','Controller'),
			'permission' => Yii::t('UserGroupsModule.admin','Permission'),
		);
	}
	
	/**
	 * return the data provider of the controller list, with module definition and 
	 * permissions checkboxes plus additional data for the other form elements
	 * @param string $what
	 * @param int $id
	 * @param array $additionalData
	 */
	public static function controllerList($group = NULL, $user = NULL)
	{	
		
		// load existing access rules
		if ($group)
			self::$_gRules = self::findRules(self::GROUP ,(int)$group);
			
		if ($user)
			self::$_uRules = self::findRules(self::USER, (int)$user);
		
		// reset the value of _rawData
		self::$_rawData = array();
		// gets the full list of declared classes
		self::$_declaredClasses = get_declared_classes();
		// reset the value of _alreadyIncluded
		self::$_alreadyIncluded = array();
		// extracts the controller data of the controllers that are part of the core web application
		self::extractControllers('application.controllers');
		// extracts the controller and module data of the controllers that belong to a module
		self::extractControllers('application.modules.*.controllers', true);
		
		return new CArrayDataProvider(self::$_rawData, array('pagination'=>false));
	}
	
	/** 
	 * return the home array list
	 * @return array
	 */
	public static function homeList()
	{
		// reset the value of _rawData
		self::$_rawData = array();
		// gets the full list of declared classes
		self::$_declaredClasses = get_declared_classes();
		// reset the value of _alreadyIncluded
		self::$_alreadyIncluded = array();
		// extracts the controller data of the controllers that are part of the core web application
		self::extractControllers('application.controllers', false, 'homeList');
		// extracts the controller and module data of the controllers that belong to a module
		self::extractControllers('application.modules.*.controllers', true, 'homeList');
		return self::$_rawData;
	}
	
	/**
	 * generates an array with controller's data
	 * @param string $where string for the getPathOfAlias yii method.
	 * @param bool $module if check or not for module names in the controllers path
	 * @param string $mode determinates what kind of array this method returns
	 */
	private static function extractControllers($where, $module = false, $mode = 'dataProvider')
	{
		foreach (glob(Yii::getPathOfAlias($where) . "/*Controller.php") as $controller){
			if ($module) {
				if (DIRECTORY_SEPARATOR === '/') // fix for windows machines 
					self::$_moduleName = preg_replace('/^.*\/modules\/(.*)\/controllers.*$/', '$1', $controller);
				else
					self::$_moduleName = preg_replace('/^.*\\\modules\\\(.*)\\\controllers.*$/', '$1', $controller);
			} else
				self::$_moduleName = 'Basic';
			$_controllerName = basename($controller, "Controller.php"); // TODO when stop supporting php 5.2 use lcfirst
			$_controllerName{0} = strtolower($_controllerName{0});
			self::$_controllerName = $_controllerName;
			 
			$controller_class = ucfirst(self::$_controllerName.'Controller');
			
			// extract the value of permission controller inside the controller
			if (!in_array($controller_class, self::$_alreadyIncluded)) {
				// use reflectionClass if a controller with the same class name was not previously included
				// add the controller class to the alreadyIncluded array
				self::$_alreadyIncluded[] = $controller_class;
				if (!in_array($controller_class, self::$_declaredClasses))
					include($controller);
				
				$class = new ReflectionClass($controller_class);
				if ($class->hasProperty('_permissionControl'))
					$permissionControl = $class->getStaticPropertyValue('_permissionControl');
				else
					$permissionControl = NULL;
			} else {
				// parse the file if a controller with the same class name was previously included
				// get the controller file content
				$controller_file = file_get_contents($controller, false, NULL, 0);
				// check if there is permissionControl inside it
				if (strpos($controller_file, 'permissionControl') !== false) {
					// get portion of the file containing permissionControl
					$controller_file = substr($controller_file, strpos($controller_file, 'permissionControl'));
					$controller_file = substr($controller_file, 0, strpos($controller_file, ';'));
					$permissionControl = eval('return $'.$controller_file.';');
				} else
					$permissionControl = NULL;
			}
			
			// check the value of permissionControl and skip this controller if necessary
			if ($permissionControl === false || (count($permissionControl) === 1 && isset($permissionControl['label']) && $mode === 'dataProvider'))
					continue;
			
			if ($mode === 'dataProvider') {	
				self::$_rawData[] = array(
					'id'=>NULL,
					'Module' => self::$_moduleName,
					'Controller' => isset($permissionControl['label']) ? $permissionControl['label'] : self::$_controllerName,
					'Read' =>  self::infoButton($permissionControl, 'read'),
					'Write' => self::infoButton($permissionControl, 'write'),
					'Admin' => self::infoButton($permissionControl, 'admin'),
				);
			} else if ($mode === 'homeList')
				self::$_rawData['/'.(self::$_moduleName === 'Basic' ? NULL : self::$_moduleName.'/') . self::$_controllerName] = (self::$_moduleName === 'Basic' ? NULL : self::$_moduleName.': ').(isset($permissionControl['label']) ? $permissionControl['label'] : self::$_controllerName);
		}
	}
	
	/**
	 * return the info button for the controller
	 * @param string $module_name
	 * @param string $controller
	 * @return string the html checkbox
	 */
	private static function infoButton($permissionControl, $mode)
	{
		$r = NULL;
		if (isset($permissionControl[$mode]) || $permissionControl === NULL) {
			// check what kind of permission are being loaded
			if (is_array(self::$_uRules)) {
				// copy the user rules inside the rules variable. Used to check later what permissions are setted
				$rules = self::$_uRules;
				$checkmark_text = Yii::t('UserGroupsModule.admin', 'Permission granted from Group');
				if (isset(self::$_gRules[self::$_moduleName][self::$_controllerName][$mode]))
					$checkmark = CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . '/checkmark.png', $checkmark_text, array('title' => $checkmark_text ));
				else
					$checkmark = NULL;
			} else {
				$rules = self::$_gRules;
				$checkmark = NULL;
			}
			// extract the text
			if ($permissionControl === NULL) {
				$info_text = Yii::t('UserGroupsModule.admin','it was not possible to get the value of permissionControl inside your controller. Check your Controller.');
				$r = 'black_';
			} elseif ($permissionControl[$mode]) {
				$info_text = Yii::t('UserGroupsModule.cont_description', ''.$permissionControl[$mode]);
			} else {
				$info_text = Yii::t('UserGroupsModule.admin','no tooltip provided');
				$r = 'def_';
			}
			return CHtml::checkBox('UserGroupsAccess[access]['.self::$_moduleName.'.'.self::$_controllerName.'.'.$mode.']', isset($rules[self::$_moduleName][self::$_controllerName][$mode])) . 
				CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/{$r}info.png", $info_text, array('class'=>'info-button', 'title'=>$info_text)) . $checkmark;
		}
		return NULL;
	}
	
	/**
	 * extract the rules and store them into an array
	 * @param int $element user or group
	 * @param int $element_id the id of the element
	 * @return array
	 */
	public static function findRules($element, $element_id)
	{
		// initialize the returning array
		$result = array();
		// extract the rules from the database
		$rules = self::model()->findAllByAttributes(array('element' => $element, 'element_id' => $element_id));
		if ($rules) {
			foreach ($rules as $rule) {
				$result[$rule->module][$rule->controller][$rule->permission] = 1;
			}
		}
		return $result;
	}
}