<?php

setlocale(LC_TIME, 'ru_RU.UTF-8');

// Социальная авторизация
$socialsConfigPath=dirname(__FILE__).DIRECTORY_SEPARATOR.'/socials.php';
$paramsConfigPath=dirname(__FILE__).DIRECTORY_SEPARATOR.'/params.php';

$mainConfig = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Rosyama',
	'language'=>'ru',
	'defaultController'=>'holes',
	'preload'=>array('log'),
	
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.classes.*',
		'application.modules.userGroups.*',
		'application.modules.userGroups.models.*',		
        'application.modules.userGroups.components.*',
        'application.modules.comments.models.*',
		'application.extensions.nestedset.*',
		'application.extensions.fpdf.*',
		'application.extensions.*',
		'application.helpers.*',
	    'ext.eoauth.*',
		'ext.eoauth.lib.*',
		'ext.lightopenid.*',
		'ext.eauth.services.*',
	),
	'modules'=>array(
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'root',
			'ipFilters'=>array('77.37.132.232', '195.91.137.124', '127.0.0.1'),
			'generatorPaths'=>array(
				'ext.giix-core',
			),
		),
		*/		
		'userGroups'=>array(
			'accessCode'=>'12345',
			'salt'=>'111',				
			'profile'=>Array('Profile')
		),
		'comments'=>array(
			'defaultModelConfig'=>array(
				'registeredOnly'=>true,
				'useCaptcha'=>false,
				'allowSubcommenting'=>true,
				'premoderate'=>false,
				'postCommentAction'=>'comments/comment/postComment',
				'isSuperuser'=>'Yii::app()->user->isModer',
				'orderComments'=>'ASC',					
			),
			'commentableModels'=>array(
				'Holes'=>array(
					'registeredOnly'=>true,
					'useCaptcha'=>false,
					'allowSubcommenting'=>true,
					'pageUrl'=>array(
						'route'=>'holes/view',
						'data'=>array(
							'id'=>'ID'
						),
					),
				),
				'ImpressionSet',
			),
			'userConfig'=>array(
				'class'=>'UserGroupsUser',
				'nameProperty'=>'fullname',
				//'emailProperty'=>'email',
			),
		),
	),
	'components'=>array(
		// Настройки для базы на продакшене
		'db'=>array(
			'class'=>'CDbConnection',
			'connectionString'=>'mysql:host=localhost;dbname=rosyama',
			'emulatePrepare'=>false,
			'username'=>'root',
			'password'=>'123',
			'charset'=>'utf8',
			'tablePrefix'=>'yii_',
			'schemaCachingDuration'=>3600,
			'enableProfiling' => YII_DEBUG,
			'enableParamLogging' => YII_DEBUG,
		),
		'user'=>array(
			'allowAutoLogin'=>true,
			'class'=>'userGroups.components.WebUserGroups',
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'urlSuffix'=>'/',
			'rules'=>array(
				'/'=>'holes/index',
				'<id:\d+>'=>'holes/view',
				'map/<userid:\d+>/'=>'holes/map',				  
				'map'=>'holes/map',
				'page/<view:\w+>/'=>'site/page',
				'userGroups'=>'userGroups',
				'gii'=>'gii',
				'profile'=>'profile',
				'sprav/<subject_id:\d+>/add/'=>'sprav/add',
				'api/<id:\d+>'=>'api/index',
				'api/my/<id:\d+>/update'=>'api/update',
				'api/my/<id:\d+>/<type:[a-zA-Z0-9\_]+>'=>'api/setstate',
				'holes/cronDaily/<type:[a-zA-Z0-9\_-]+>'=>'holes/cronDaily',
				'<controller:\w+>'=>'<controller>/index',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'image'=>array(
          'class'=>'application.extensions.image.CImageComponent',
            'driver'=>'GD',
            'params'=>array('directory'=>'/opt/local/bin'),
        ),
		'loid' => array(
			'class'=>'application.extensions.lightopenid.loid',
		),
		'eauth' => array(
			'class'=>'ext.eauth.EAuth',
			'popup'=>true,
			'services'=>file_exists($socialsConfigPath) ? require_once $socialsConfigPath : array(),
		),
		'cache'=>array(
            'class'=>'system.caching.CApcCache',          
        ),
		'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),
		'widgetFactory'=>array(
			'enableSkin'=>true,
            'widgets'=>array(
                /*'CGridView'=>array(
                    'cssFile'=>'/css/gridview/styles.css',
                ),
                'CTabView'=>array(
                    'cssFile'=>'/css/CTabView/styles.css',
                ),
                'CDetailView'=>array(
                    'cssFile'=>'/css/CDetailView/styles.css',
                ),*/
                'CJuiDatePicker'=>array(
                    'language'=>'ru',
                ),
                'CLinkPager'=>array(
                    'maxButtonCount'=>10,
                    //'cssFile'=>false,
                ),
            ),
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'application.extensions.pqp.PQPLogRoute',
                    'categories'=>'application.*, exception.*',
                ),
            ),
			'enabled'=>YII_DEBUG,
		),
	),
	'params'=>file_exists($paramsConfigPath) ? require_once $paramsConfigPath : array(),
);

/**
 * Чтобы указать свои параметры, создайте файл dev.main.php
 * Пример файла:
 
return CMap::mergeArray(
	$mainConfig,
	array(
		'components'=>array(
			'db'=>array(
				'class'=>'CDbConnection',
				'connectionString'=>'mysql:host=localhost;dbname=rosyama',
				'emulatePrepare'=>false,
				'username'=>'root',
				'password'=>'123',
				'charset'=>'utf8',
				'tablePrefix'=>'yii_',
				'schemaCachingDuration'=>3600,
				'enableProfiling'=>YII_DEBUG,
				'enableParamLogging'=>YII_DEBUG,
			),
		),
	),
);
*/

$devPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'/dev.main.php';
if (file_exists($devPath))
{
	$mainConfig = require_once $devPath;
}
return $mainConfig;
