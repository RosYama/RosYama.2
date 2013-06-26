<?php

$paramsConfigPath=dirname(__FILE__).DIRECTORY_SEPARATOR.'/params.php';

$mainXmlConfig = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Rosyama',
	'language'=>'ru',
	'defaultController'=>'xml',
	'preload'=>array('log'),	
	
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.classes.*',
		'application.modules.userGroups.*',
		'application.modules.userGroups.models.*',
        'application.modules.userGroups.components.*',
		'application.extensions.nestedset.*',
		'application.extensions.fpdf.*',
		'application.extensions.*',
		'application.modules.comments.models.*',
		'application.helpers.*',
	    'ext.eoauth.*',
		'ext.eoauth.lib.*',
		'ext.lightopenid.*',
		'ext.eauth.services.*',
	),
	'modules'=>array(		
		'userGroups'=>array(
			'accessCode'=>'12345',
			'salt'=>'111',				
			'profile'=>Array('Profile')
		),
		'comments'=>array(
			'defaultModelConfig'=>array(
				'registeredOnly'=>true,
				'useCaptcha'=>false,
				'allowSubcommenting'=> rue,
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
						'data'=>array('id'=>'ID'),
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
	// application components
	'components'=>array(
		'user'=>array(
      	'allowAutoLogin'=>true,
		'class'=>'userGroups.components.WebUserGroups',

		),
		'errorHandler'=>array(
            'errorAction'=>'xml/error',
        ),
        'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'urlSuffix'=>'/',
			'rules'=>array(
				'/'=>'xml/index',
				'/<id:\d+>'=>'xml/index',
				'/my/<id:\d+>/update'=>'xml/update',
				'/my/<id:\d+>/<type:[a-zA-Z0-9\_]+>'=>'xml/setstate',
				'/<action:\w+>'=>'xml/<action>',
				'/<action:\w+>/<id:\d+>'=>'xml/<action>',
			),
		),
		'image'=>array(
          'class'=>'application.extensions.image.CImageComponent',
            'driver'=>'GD',
            'params'=>array('directory'=>'/opt/local/bin'),
        ),
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
	),
	'params'=>file_exists($paramsConfigPath) ? require_once $paramsConfigPath : array(),
);

/**
 * Чтобы указать свои параметры, создайте файл dev.xml.php
 * Пример файла:
 
return CMap::mergeArray(
	$mainXmlConfig,
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

$devPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'/dev.xml.php';
if (file_exists($devPath))
{
	$mainXmlConfig = require_once $devPath;
}
return $mainXmlConfig;
