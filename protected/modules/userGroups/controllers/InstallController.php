<?php
/**
 * @author Nicola Puddu
 * @package userGroups
 * perform module installation
 *
 */
class InstallController extends Controller
{
	/**
	 * @var mixed no permission rules for this controller
	 */
	public static $_permissionControl = false;

	/**
	 * render the installation views
	 */
	public function actionIndex()
	{
		$model= new UserGroupsInstallation('access_code');

		// collect user input data
		if(isset($_POST['UserGroupsInstallation']))
		{
			// access code control
			if ($_POST['action'] === 'accesscode') {
				$model->attributes=$_POST['UserGroupsInstallation'];
				// validate user input and shows the form for completing installation
				if($model->validate()) {
					$model= new UserGroupsInstallation('installation');
				}
			// actual installation
			}elseif ($_POST['action'] === 'installation') {
				$model= new UserGroupsInstallation('installation');
				$this->performAjaxValidation($model);
				$model->attributes=$_POST['UserGroupsInstallation'];
				// validate user input and creates the userGroups tables in the database
				if($model->validate()) {
					$this->createController();
					$this->createTables();
					$this->initializeConfiguration();
					$this->initializeCron();
					$this->populateLookup();
					$this->initializeGroups();
					$this->createRootUser();
					$this->createMailViews();
					Yii::app()->user->setFlash('success',Yii::t('userGroupsModule.install', '<p>userGroups Installed! To continue the configuration please login</p><p>The new Controller class file has been created. Your old controller has been saved in the component folder with the name <i>_old_Controller.php</i></p>'));
					$this->redirect(Yii::app()->baseUrl . '/userGroups');
				}
			}
		}

		// display the installation forms
		$this->render('index', array('model' => $model));
	}

	/**
	 * creates the new controller file from the template, saving the old controller naming it _old_Controller.php
	 */
	private function createController()
	{
		// save the old Controller in the components folder of the main application naming it _old_Controller.php
		$path = Yii::app()->basePath . '/components/_old_Controller.php';
		$content = file_get_contents(Yii::app()->basePath . '/components/Controller.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}
		// write the new UserIdentity file in the components folder of the main application
		$path = Yii::app()->basePath . '/components/Controller.php';
		$content = file_get_contents(Yii::app()->basePath . '/modules/userGroups/templates/template_Controller.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}
	}

	/**
	 * creates the mail view files
	 * @return boolean true
	 */
	private function createMailViews()
	{
		mkdir(Yii::app()->basePath.'/views/ugmail');
		// add the activation mail view
		$path = Yii::app()->basePath . '/views/ugmail/activation.php';
		$content = file_get_contents(Yii::app()->basePath . '/modules/userGroups/templates/template_activation.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}
		// add the invitation mail view
		$path = Yii::app()->basePath . '/views/ugmail/invitation.php';
		$content = file_get_contents(Yii::app()->basePath . '/modules/userGroups/templates/template_invitation.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}
		// add the password reset mail view
		$path = Yii::app()->basePath . '/views/ugmail/passreset.php';
		$content = file_get_contents(Yii::app()->basePath . '/modules/userGroups/templates/template_passreset.php');
		if(@file_put_contents($path,$content)===false) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to write the file {path}.', array('path'=>$path)));
			return false;
		}

		return true;
	}

	/**
	 * launch the sql commands to create the database tables
	 */
	private function createTables()
	{
		// gets the database connection
		$connection = Yii::app()->db;
		// detects the database driver
		$driver = $connection->driverName;
		// get the table prefix
		$tablePrefix = $connection->tablePrefix;
		// starts the transactions
		$transaction=$connection->beginTransaction();
		try
		{
			// creates the tables
			$connection->createCommand($this->configurationTable($driver, $tablePrefix))->execute();
			$connection->createCommand($this->cronTable($driver, $tablePrefix))->execute();
			$connection->createCommand($this->userTable($driver, $tablePrefix))->execute();
			$connection->createCommand($this->groupTable($driver, $tablePrefix))->execute();
			$connection->createCommand($this->accessTable($driver, $tablePrefix))->execute();
			$connection->createCommand($this->lookupTable($driver, $tablePrefix))->execute();
			// creates the relationship
			$connection->createCommand($this->relationsQuery($driver, $tablePrefix))->execute();
			$transaction->commit();
		}
		catch(Exception $e)
		{
			$transaction->rollBack();
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Unable to create the tables in the database. Make sure that your db user has the necessary permissions to create a new table.<br/>This release only officially supports Mysql and PostgreSQL.'));
			return false;
		}
	}

	/**
	 * creates the basic configurations
	 */
	private function initializeConfiguration()
	{
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'version';
		$configuration_model->value = UserGroupsInstallation::VERSION;
		$configuration_model->options = 'CONST';
		$configuration_model->description = 'userGroups version';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'password_strength';
		$configuration_model->value = '0';
		$configuration_model->options = 'a:3:{i:0;s:4:"weak";i:1;s:6:"medium";i:2;s:6:"strong";}';
		$configuration_model->description = 'password strength:<br/>weak: password of at least 5 characters, any character allowed.<br/>
			medium: password of at least 5 characters, must contain at least 2 digits and 2 letters.<br/>
			strong: password of at least 5 characters, must contain at least 2 digits, 2 letters and a special character.';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'registration';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'allow user registration';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'public_user_list';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'logged users can see the complete user list';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'public_profiles';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'allow everyone, even guests, to see user profiles';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'profile_privacy';
		$configuration_model->value = 'TRUE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'logged user can see other users profiles';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'personal_home';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'users can set their own home';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'simple_password_reset';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'if true users just have to provide user and email to reset their password.<br/>Otherwise they will have to answer their custom question';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'user_need_activation';
		$configuration_model->value = 'TRUE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'if true when a user creates an account a mail with an activation code will be sent to his email address';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'user_need_approval';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'if true when a user creates an account a user with user admin rights will have to approve the registration.<br/>If both this setting and user_need_activation are true the user will need to activate is account first and then will need the approval';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'user_registration_group';
		$configuration_model->value = '2';
		$configuration_model->options = 'GROUP_LIST';
		$configuration_model->description = 'the group new users automatically belong to';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'dumb_admin';
		$configuration_model->value = 'TRUE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'users with just admin write permissions won\'t see the Main Configuration and Cron Jobs panels';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'super_admin';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'users with userGroups admin admin permission will have access to everything, just like root';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'permission_cascade';
		$configuration_model->value = 'TRUE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'if a user has on a controller admin permissions will have access to write and read pages. If he has write permissions will also have access to read pages';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
		$configuration_model = new UserGroupsConfiguration('installation');
		$configuration_model->rule = 'server_executed_crons';
		$configuration_model->value = 'FALSE';
		$configuration_model->options = 'BOOL';
		$configuration_model->description = 'if true crons must be executed from the server using a crontab';
		if (!$configuration_model->save())
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Setting not installed. Installation abort.'));
	}

	/**
	 * initialize the list of cron jobs to perform
	 */
	private function initializeCron()
	{
		$cron_model = new UserGroupsCron;
		$cron_model->name = 'garbage_collection';
		$cron_model->lapse = 7;
		$cron_model->save();
		$cron_model = new UserGroupsCron;
		$cron_model->name = 'unban';
		$cron_model->lapse = 1;
		$cron_model->save();
	}

	/**
	 * creates the default items in the lookup table
	 */
	private function populateLookup()
	{
		$lookup_model = new UserGroupsLookup;
		$lookup_model->element = 'status';
		$lookup_model->value = UserGroupsUser::BANNED;
		$lookup_model->text = 'banned';
		$lookup_model->save();
		$lookup_model = new UserGroupsLookup;
		$lookup_model->element = 'status';
		$lookup_model->value = UserGroupsUser::WAITING_ACTIVATION;
		$lookup_model->text = 'waiting activation';
		$lookup_model->save();
		$lookup_model = new UserGroupsLookup;
		$lookup_model->element = 'status';
		$lookup_model->value = UserGroupsUser::WAITING_APPROVAL;
		$lookup_model->text = 'waiting approval';
		$lookup_model->save();
		$lookup_model = new UserGroupsLookup;
		$lookup_model->element = 'status';
		$lookup_model->value = UserGroupsUser::PASSWORD_CHANGE_REQUEST;
		$lookup_model->text = 'password change request';
		$lookup_model->save();
		$lookup_model = new UserGroupsLookup;
		$lookup_model->element = 'status';
		$lookup_model->value = UserGroupsUser::ACTIVE;
		$lookup_model->text = 'active';
		$lookup_model->save();
	}

	/**
	 * creates the root group and the user group
	 */
	private function initializeGroups()
	{
		// root group
		$group_model = new UserGroupsGroup('installation');
		$group_model->id = UserGroupsUser::ROOT;
		$group_model->groupname = 'root';
		$group_model->level = UserGroupsUser::ROOT_LEVEL;
		$group_model->save();
		// check if the root group was created with the right id
		if ((int)$group_model->id !== UserGroupsUser::ROOT) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Error during installation. Couldn\'t create root group with id {id}', array('{id}'=>UserGroupsUser::ROOT)));
			Yii::app()->end();
		}
		// user group
		$group_model = new UserGroupsGroup('installation');
		$group_model->groupname = 'user';
		$group_model->home = '/userGroups';
		$group_model->level = 1;
		$group_model->save();
	}

	/**
	 * create the root user
	 */
	private function createRootUser()
	{
		$user_model = new UserGroupsUser('installation');
		$user_model->id=UserGroupsUser::ROOT;
		$user_model->username=$_POST['UserGroupsInstallation']['root_user'];
		$user_model->password=$_POST['UserGroupsInstallation']['root_password'];
		$user_model->email=$_POST['UserGroupsInstallation']['root_email'];
		$user_model->group_id=UserGroupsUser::ROOT;
		$user_model->status=UserGroupsUser::ACTIVE;
		$user_model->question=$_POST['UserGroupsInstallation']['root_question'];
		$user_model->answer=$_POST['UserGroupsInstallation']['root_answer'];
		$user_model->home='/userGroups/admin/documentation';
		$user_model->save();
		if ((int)$user_model->id !== UserGroupsUser::ROOT) {
			throw new CHttpException (500, Yii::t('userGroupsModule.install', 'Error during installation. Couldn\'t create root user with id {id}', array('{id}'=>UserGroupsUser::ROOT)));
			Yii::app()->end();
		}
	}

	/**
	 * returns the query to create the usergroups_user table
	 * @param string $driver database driver in use
	 * @return string
	 */
	private function userTable($driver, $tablePrefix)
	{
		switch ($driver) {
			case 'mysql':
				return "CREATE TABLE {$tablePrefix}usergroups_user
				(
				id BIGINT AUTO_INCREMENT,
				group_id BIGINT(20),
				username VARCHAR(120) NOT NULL UNIQUE,
				password VARCHAR(120),
				email VARCHAR(120) NOT NULL UNIQUE,
				home VARCHAR(120) DEFAULT NULL,
				status TINYINT(1) DEFAULT 1 NOT NULL,
				question TEXT,
				answer TEXT,
				creation_date DATETIME,
				activation_code VARCHAR(30) DEFAULT NULL,
				activation_time DATETIME,
				last_login DATETIME,
				ban DATETIME,
				ban_reason TEXT,
				PRIMARY KEY (id)
				) ENGINE=InnoDB;";
				break;
			case 'pgsql':
				return "CREATE TABLE {$tablePrefix}usergroups_user
				(
				id BIGSERIAL,
				group_id BIGINT,
				username VARCHAR(120) NOT NULL UNIQUE,
				password VARCHAR(120),
				email VARCHAR(120) NOT NULL UNIQUE,
				home VARCHAR(120) DEFAULT NULL,
				status INT NOT NULL DEFAULT 1,
				question TEXT,
				answer TEXT,
				creation_date TIMESTAMP,
				activation_code VARCHAR(30) DEFAULT NULL,
				activation_time TIMESTAMP,
				last_login TIMESTAMP,
				ban TIMESTAMP,
				ban_reason TEXT,
				PRIMARY KEY (id)
				);";
				break;
			default:
				return "CREATE TABLE {$tablePrefix}usergroups_user
				(
				id BIGINT AUTO_INCREMENT,
				group_id BIGINT(20),
				username VARCHAR(120) NOT NULL UNIQUE,
				password VARCHAR(120),
				email VARCHAR(120) NOT NULL UNIQUE,
				home VARCHAR(120) DEFAULT NULL,
				status TINYINT(1) DEFAULT '1' NOT NULL,
				question TEXT,
				answer TEXT,
				creation_date TIMESTAMP,
				activation_code VARCHAR(30) DEFAULT NULL,
				activation_time TIMESTAMP,
				last_login TIMESTAMP,
				ban TIMESTAMP,
				ban_reason TEXT,
				PRIMARY KEY (id)
				);";
				break;
		}
	}

	/**
	 * returns the query to create the usergroups_group table
	 * @param string $driver database driver in use
	 * @return string
	 */
	private function groupTable($driver, $tablePrefix)
	{
		switch ($driver) {
			case 'mysql':
				return "CREATE TABLE {$tablePrefix}usergroups_group
				(
				id BIGINT(20) AUTO_INCREMENT,
				groupname VARCHAR(120) NOT NULL UNIQUE,
				level INT(6),
				home VARCHAR(120) DEFAULT NULL,
				PRIMARY KEY (id)
				) ENGINE=InnoDB;";
				break;
			case 'pgsql':
				return "CREATE TABLE {$tablePrefix}usergroups_group
				(
				id BIGSERIAL,
				groupname VARCHAR(120) NOT NULL UNIQUE,
				level INTEGER,
				home VARCHAR(120) DEFAULT NULL,
				PRIMARY KEY (id)
				);";
				break;
			default:
				return "CREATE TABLE {$tablePrefix}usergroups_group
				(
				id BIGINT(20) AUTO_INCREMENT,
				groupname VARCHAR(120) NOT NULL UNIQUE,
				level INT(6),
				home VARCHAR(120) DEFAULT NULL,
				PRIMARY KEY (id)
				);";
				break;
		}
	}

	/**
	 * returns the query to create the usergroups_group table
	 * @param string $driver database driver in use
	 * @return string
	 */
	private function lookupTable($driver, $tablePrefix)
	{
		switch ($driver) {
			case 'mysql':
				return "CREATE TABLE {$tablePrefix}usergroups_lookup
				(
				id BIGINT(20) AUTO_INCREMENT,
				element VARCHAR(20),
				value INTEGER(5),
				text VARCHAR(40),
				PRIMARY KEY (id)
				);";
				break;
			case 'pgsql':
				return "CREATE TABLE {$tablePrefix}usergroups_lookup
				(
				id BIGSERIAL,
				element VARCHAR(20),
				value INTEGER,
				text VARCHAR(40),
				PRIMARY KEY (id)
				);";
				break;
			default:
				return "CREATE TABLE {$tablePrefix}usergroups_lookup
				(
				id BIGINT(20) AUTO_INCREMENT,
				element VARCHAR(20),
				value INTEGER(5),
				text VARCHAR(40),
				PRIMARY KEY (id)
				);";
				break;
		}
	}

	/**
	 * returns the query to create the usergroups_configuration table
	 * @param string $driver database driver in use
	 * @return string
	 */
	private function configurationTable($driver, $tablePrefix)
	{
		switch ($driver) {
			case 'mysql':
				return "CREATE TABLE {$tablePrefix}usergroups_configuration
				(
				id BIGINT(20) AUTO_INCREMENT,
				rule VARCHAR(40),
				value VARCHAR(20),
				options TEXT,
				description TEXT,
				PRIMARY KEY (id)
				);";
				break;
			case 'pgsql':
				return "CREATE TABLE {$tablePrefix}usergroups_configuration
				(
				id BIGSERIAL,
				rule VARCHAR(40),
				value VARCHAR(20),
				options TEXT,
				description TEXT,
				PRIMARY KEY (id)
				);";
				break;
			default:
				return "CREATE TABLE {$tablePrefix}usergroups_configuration
				(
				id BIGINT(20) AUTO_INCREMENT,
				rule VARCHAR(40),
				value VARCHAR(20),
				options TEXT,
				description TEXT,
				PRIMARY KEY (id)
				);";
				break;
		}
	}

	/**
	 * returns the query to create the usergroups_cron table
	 * @param string $driver the database driver in use
	 * @return string
	 */
	private function cronTable($driver, $tablePrefix)
	{
		switch ($driver) {
			case 'mysql':
				return "CREATE TABLE {$tablePrefix}usergroups_cron
				(
				id BIGINT(20) AUTO_INCREMENT,
				name VARCHAR(40),
				lapse INT(6),
				last_occurrence DATETIME,
				PRIMARY KEY (id)
				);";
				break;
			case 'pgsql':
				return "CREATE TABLE {$tablePrefix}usergroups_cron
				(
				id BIGSERIAL,
				name VARCHAR(40),
				lapse INT,
				last_occurrence TIMESTAMP,
				PRIMARY KEY (id)
				);";
				break;
			default:
				return "CREATE TABLE {$tablePrefix}usergroups_cron
				(
				id BIGINT(20) AUTO_INCREMENT,
				name VARCHAR(40),
				lapse INT(6),
				last_occurrence TIMESTAMP,
				PRIMARY KEY (id)
				);";
				break;
		}
	}

	/**
	 * returns the query to create the usergroups_access table
	 * @param string $driver the database driver in use
	 * @return string
	 */
	private function accessTable($driver, $tablePrefix)
	{
		switch ($driver) {
			case 'mysql':
				return "CREATE TABLE {$tablePrefix}usergroups_access
				(
				id BIGINT(20) AUTO_INCREMENT,
				element INT(3) NOT NULL,
				element_id BIGINT(20) NOT NULL,
				module VARCHAR(140) NOT NULL,
				controller VARCHAR(140) NOT NULL,
				permission VARCHAR(7) NOT NULL,
				PRIMARY KEY (id)
				);";
				break;
			case 'pgsql':
				return "CREATE TABLE {$tablePrefix}usergroups_access
				(
				id BIGSERIAL,
				element INT NOT NULL,
				element_id BIGINT NOT NULL,
				module VARCHAR(140) NOT NULL,
				controller VARCHAR(140) NOT NULL,
				permission VARCHAR(7) NOT NULL,
				PRIMARY KEY (id)
				);";
				break;
			default:
				return "CREATE TABLE {$tablePrefix}usergroups_access
				(
				id BIGINT(20) AUTO_INCREMENT,
				element INT(3) NOT NULL,
				element_id BIGINT(20) NOT NULL,
				module VARCHAR(140) NOT NULL,
				controller VARCHAR(140) NOT NULL,
				permission VARCHAR(7) NOT NULL,
				PRIMARY KEY (id)
				);";
				break;
		}
	}

	/**
	 * returns the query that creates the relationship among the two tables
	 * @param string $driver database driver in use
	 * @return string
	 */
	private function relationsQuery($driver, $tablePrefix)
	{
		switch ($driver) {
			case 'mysql':
				return "ALTER TABLE {$tablePrefix}usergroups_user ADD FOREIGN KEY group_id_idxfk (group_id) REFERENCES {$tablePrefix}usergroups_group (id) ON DELETE CASCADE;";
				break;
			case 'pgsql':
				return "ALTER TABLE {$tablePrefix}usergroups_user ADD FOREIGN KEY (group_id) REFERENCES {$tablePrefix}usergroups_group (id) ON DELETE CASCADE;";
				break;
			default:
				return "ALTER TABLE {$tablePrefix}usergroups_user ADD FOREIGN KEY (group_id) REFERENCES {$tablePrefix}usergroups_group (id) ON DELETE CASCADE;";
				break;
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']))
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}