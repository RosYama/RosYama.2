<?php
/**
 * this file contains the UGCron class that is responsible for executing the module crons
 * @author Nicola Puddu
 * @package userGroups
 */

/**
 * singleton that is used to store and exec the cron jobs
 * @author Nicola Puddu
 * @package userGroups
 */
class UGCron {
	/**
	 * contains the instance of the UGCron singleton
	 * @var UGCron
	 */
	private static $object = NULL;
	/**
	 * array containing the UGCronJob objects
	 * @var array
	 */
	private $crons = array();
	/**
	 * array containing the status of the cronjobs
	 * @var array
	 */
	private $status = array();
	/**
	 * array containing the cronjobs integrity errors
	 * @var array
	 */
	private $errors = array();
	/**
	 * array containing the cronjobs description
	 * @var array
	 */
	private $descriptions = array();
	/**
	 * constant rappresenting a not initialized status of UGCron
	 * @var unknown_type
	 */
	const NOT_INITIALIZED = 0;
	/**
	 * constants rappresenting the status of the cronjobs
	 * @var int
	 */
	const NOT_INSTALLED = 1;
	const ERRORS = 2;
	const OK = 3;
	
	/**
	 * private __construct make this class a singleton
	 */
	private function __construct()
	{
	}
	
	/**
	 * initialize the singleton
	 */
	public static function init()
	{
		if (!self::$object)
			self::$object = new self();
		return self::$object;
	}
	
	/**
	 * add a cronjob to UGCron
	 * @param UGCronJob $cronjob
	 */
	public static function add(UGCronJob $cronjob)
	{
		// check if the class was initialized
		if (!self::$object)
			throw new CHttpException('500', Yii::t('userGroupsModule.cron','you didn\'t initialize the UGCron singleton'));
		
		// check if the cronjob was already added
		if (!isset(self::$object->status[$cronjob->name])) {
			// add the cron description to the descriptions array
			self::$object->descriptions = array_merge(self::$object->descriptions, array($cronjob->name => $cronjob->description));
			$integrity = $cronjob->checkIntegrity($cronjob);
			// check if the model was returned and in this case add the cronjob to
			// the crons array, otherwise add the error to the errors array
			if ($integrity instanceof CActiveRecord) {
				$cronjob->_model = $integrity;
				self::$object->crons[] = $cronjob;
				self::$object->status = array_merge(self::$object->status, array($cronjob->name => self::OK));
			} else {
				self::$object->errors = array_merge(self::$object->errors, $integrity);
				self::$object->status = array_merge(self::$object->status, array($cronjob->name => self::ERRORS));
			}
		}
	}
	
	
	public static function getStatus($cronName, $string = false, $print = false)
	{
		// get the status code of the cronjob
		if (!self::$object)
			$status = self::NOT_INITIALIZED;
		else
			$status = isset(self::$object->status[$cronName]) ? self::$object->status[$cronName] : self::NOT_INSTALLED;
			
		// if string is true extract the corresponding string to the status
		if ($string) {
			if ($status === self::OK)
				$return_value = Yii::t('userGroupsModule.cron', 'installed and running');
			elseif ($status === self::ERRORS)
				$return_value = self::getErrors($cronName);
			elseif ($status === self::NOT_INSTALLED)
				$return_value =  Yii::t('userGroupsModule.cron', 'not installed');
			elseif ($status === self::NOT_INITIALIZED)
				$return_value = Yii::t('userGroupsModule.cron','you didn\'t initialize the UGCron singleton');
		} else
			$return_value = $status;
		
		// if print is true print the return_value, otherwise return it
		if ($print)
			echo $return_value;
		else
			return $return_value;
	}
	
	/**
	 * return the errors array registered when loading the cron jobs
	 * you can optionally provide the cron name to get just a specific cron error
	 * if print is true it prints the error instead of returning it
	 * @param string $cron
	 * @param bool $print
	 */
	public static function getErrors($cronName = NULL, $print = false)
	{
		if (!self::$object)
			$return_value = Yii::t('userGroupsModule.cron','you didn\'t initialize the UGCron singleton');
		elseif ($cronName)
			$return_value = isset(self::$object->errors[$cronName]) ? self::$object->errors[$cronName] : 
				Yii::t('userGroupsModule.cron', 'not installed');
		else
			$return_value = self::$object->errors;
		
		if ($print)
			echo is_array($return_value) ? implode(', ',$return_value) : $return_value;
		else
			return $return_value;
	}
	
	/**
	 * return the descriptions array registered when loading the cron jobs
	 * you can optionally provide the cron name to get just a specific cron description
	 * if print is true it prints the error instead of returning it
	 * @param string $cron
	 * @param bool $print
	 */
	public static function getDescriptions($cronName = NULL, $print = false)
	{
		if (!self::$object)
			$return_value = Yii::t('userGroupsModule.cron','you didn\'t initialize the UGCron singleton');
		elseif ($cronName)
			$return_value = isset(self::$object->descriptions[$cronName]) ? self::$object->descriptions[$cronName] : 
				Yii::t('userGroupsModule.cron', 'No description found for the requested cron job');
		else
			$return_value = self::$object->descriptions;
		
		if ($print)
			echo is_array($return_value) ? implode(', ',$return_value) : $return_value;
		else
			return $return_value;
	}
	
	/**
	 * return the boolean indicating whether the cron is istalled or not
	 * @param string $cronName
	 * @return bool
	 */
	public static function isInstalled($cronName)
	{
		return isset(self::$object->errors[$cronName]) ? true : false;
	}
	
	/**
	 * run the loaded cronjobs
	 */
	public static function run()
	{
		// check if the class was initialized
		if (!self::$object)
			throw new CHttpException('500', Yii::t('userGroupsModule.cron','you didn\'t initialize the UGCron singleton'));
		// array containing the loaded cronTables
		$loaded_cron_tables = array();
		foreach (self::$object->crons as $cron) {
			// load the model cronTable model
			if (!isset($loaded_cron_tables[$cron->cronTable])) {
				$cTable = new $cron->cronTable;
				$loaded_cron_tables[$cron->cronTable] = $cTable;
			} else
				$cTable = $loaded_cron_tables[$cron->cronTable];
			// define the attributes for loading the cronjob parameters on the cronTable
			$attributes = array('name' => $cron->name);

			// load the cron job from the cronTable
			$cItem = $cTable->findByAttributes($attributes);
			if ($cItem) {
				// execute the cronjob if enough time has passed by
				if ($cItem->last_occurrence <= date('Y-m-d', time() - (3600 * 24 * $cItem->lapse)).' 00:00:00') {
					if ($cron->action === UGCronJob::DELETE)
						$cron->_model->deleteAll($cron->criteria);
					elseif ($cron->action === UGCronJob::UPDATE)
						$cron->_model->updateAll($cron->columns, $cron->criteria);
					// update the cronTable
					$cItem->last_occurrence = date('Y-m-d').' 00:00:00';
					$cItem->save();
				}
			} else
				self::$object->errors = array_merge(self::$object->errors, array($cron->name => Yii::t('userGroupsModule.cron', 'Cronjob not installed')));
		}
	}	
}

/**
 * @author Nicola Puddu
 * @package userGroups
 * abstract class defining cronjobs
 */
abstract class UGCronJob {
	const DELETE = 0;
	const UPDATE = 1;
	
	private $_model;
	
	/**
	 * this method check if the values of the cron job are setted
	 * to perform the action
	 * @param UGCronJob $instance
	 * @return bool
	 */
	public final function checkIntegrity(UGCronJob $instance)
	{
		// check the integrity
		if (!$instance->cronTable || !is_string($instance->cronTable))
			throw new CHttpException('500', Yii::t('userGroupsModule.cron', 'You didn\'t set the cron table name'));
		$name = $instance->name;
		if (!$name)
			throw new CHttpException('500', Yii::t('userGroupsModule.cron', 'You have to provide a name for the cronjob'));
		$model = $instance->model;
		if (!$model instanceof CActiveRecord)
			return array($name => Yii::t('userGroupsModule.cron', 'the model provided is not an instance of CActiveRecord'));
		if (!$instance->criteria instanceof CDbCriteria)
			return array($name => Yii::t('userGroupsModule.cron', 'the provided criteria are not an instance of CDbCriteria'));
		if ($instance->action !== self::DELETE && $instance->action !== self::UPDATE)
			return array($name => Yii::t('userGroupsModule.cron', 'you didn\'t provide a valid action'));
		elseif ($instance->action === self::UPDATE && (!is_array($instance->columns) || !count($instance->columns)))
			return array($name => Yii::t('userGroupsModule.cron', 'you didn\'t set the columns to update'));
			
		// check if the cron is installed and install it if not
		$cTable = new $instance->cronTable;
		$attributes = array('name' => $instance->name);
		// load the cron job from the cronTable
		$cItem = $cTable->findByAttributes($attributes);
		if ($cItem === NULL)
			if (!$this->installCron($cTable, $name, $instance->lapse))
				return array($name => Yii::t('userGroupsModule.cron', 'could not install the module. Lapse must be an integer'));
		
		
		return $model;	
	}
	
	/**
	 * install the cronjob on its cron table
	 * @param CActiveRecord $cTable
	 * @param string $name
	 * @param int $lapse
	 * @return bool
	 */
	private final function installCron(CActiveRecord $cTable, $name, $lapse)
	{
		if (!is_numeric($lapse))
			return false;
		
		$cTable->name = $name;
		$cTable->lapse = (int)$lapse;
		if ($cTable->save())
			return true;
		else
			return false;
	}
	
	/**
	 * magic method used to retrieve undeclared attributes
	 * @param string $attribute
	 */
	public final function __get($attribute)
	{
		$method = 'get'.ucfirst($attribute);
		if (method_exists($this,$method))
			return $this->$method();
		return false;
	}
	
	/**
	 * this method returns the description of the cron
	 * @return string
	 * @abstract
	 */
	abstract protected function getDescription();
	/**
	 * this method returns the cron CActiveRecord model name where 
	 * keep track of the cron
	 * @return string
	 * @abstract
	 */
	abstract protected function getCronTable();
	/**
	 * returns the name of the cronjob
	 * if a string is returned, its value will be
	 * matched with the one of the column 'name' in the cron table.
	 * @return mixed
	 * @abstract
	 */
	abstract protected function getName();
	/**
	 * returns the number of days that have to pass between
	 * two executions
	 * @return int
	 * @abstract
	 */
	abstract protected function getLapse();
	/**
	 * this method returns the CActiveRecord model
	 * @return CActiveRecord
	 * @abstract
	 */
	abstract protected function getModel();
	/**
	 * this method returns the criteria for the query
	 * a CDbCriteria object is expected to be returned
	 * if you don't need criteria return NULL.
	 * @return mixed
	 * @abstract
	 */
	abstract protected function getCriteria();
	/**
	 * this method returns a constant, possible values are:
	 * self::DELETE and self::UPDATE
	 * @return int
	 * @abstract
	 */
	abstract protected function getAction();
	/**
	 * this mathod returns an array where the key is the database
	 * column and the value is the new value.
	 * You can return null or an empty array if the action is self::DELETE.
	 * @return array
	 * @abstract
	 */
	abstract protected function getColumns();
}

/**
 * @author Nicola Puddu
 * @see UGCCronJob
 * deletes users that have not been activated for more then 7 days
 */
class UGCJGarbageCollection extends UGCronJob {
	/**
	 * return the cron description
	 */
	protected function getDescription()
	{
		return Yii::t('userGroupsModule.cron', 'delete users who have not been activated for more then 7 days');
	}
	
	/**
	 * return the cron table model name
	 */
	protected function getCronTable()
	{
		return 'UserGroupsCron';
	}
	
	/**
	 * return the cron name
	 */
	protected function getName()
	{
		return 'garbage_collection';
	}
	
	/**
	 * return the days that have to pass before performing this cron job
	 */
	protected function getLapse()
	{
		return 7;
	}
	
	/**
	 * return the model
	 */
	protected function getModel()
	{
		return new UserGroupsUser;
	}
	
	/**
	 * return the action method
	 */
	protected function getAction()
	{
		return self::DELETE;
	}
	
	/**
	 * return the action criteria
	 */
	protected function getCriteria()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('status',UserGroupsUser::WAITING_ACTIVATION);
		$criteria->compare('activation_time <', date('Y-m-d', time() - (3600 * 24 * 7)).' 00:00:00');
		return $criteria;
	}
	
	/**
	 * return the interested database fields
	 */
	protected function getColumns()
	{
		return NULL;
	}
}

/**
 * @author Nicola Puddu
 * @see UGCCronJob
 * unban users
 */
class UGCJUnban extends UGCronJob {
	/**
	 * return the cron description
	 */
	protected function getDescription()
	{
		return Yii::t('userGroupsModule.cron', 'reactivate users whose ban period is over');
	}
	
	/**
	 * return the cron table model name
	 */
	protected function getCronTable()
	{
		return 'UserGroupsCron';
	}
	
	/**
	 * return the cron name
	 */
	protected function getName()
	{
		return 'unban';
	}
	
	/**
	 * returns the number of days that have to pass before performing this cron job
	 */
	protected function getLapse()
	{
		return 1;
	}
	
	/**
	 * return the model
	 */
	protected function getModel()
	{
		return new UserGroupsUser;
	}
	
	/**
	 * return the action method
	 */
	protected function getAction()
	{
		return self::UPDATE;
	}
	
	/**
	 * return the action criteria
	 */
	protected function getCriteria()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('status',UserGroupsUser::BANNED);
		$criteria->compare('ban <', date('Y-m-d').' 00:00:00');
		return $criteria;
	}
	
	/**
	 * return the interested database fields
	 */
	protected function getColumns()
	{
		return array('status' => UserGroupsUser::ACTIVE);
	}
}