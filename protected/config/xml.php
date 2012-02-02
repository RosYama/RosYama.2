<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Rosyama',
	'language'=>'ru',
	'defaultController'=>'xml',
	// preloading 'log' component
	//'layout'=>'startpage',
	'preload'=>array('log'),	
	

	// autoloading model and component classes
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
			)
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
		// uncomment the following to enable URLs in path-format

        'urlManager'=>array(
			//'baseUrl'=>'/',
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
            // GD or ImageMagick
            'driver'=>'GD',
            // ImageMagick setup path
            'params'=>array('directory'=>'/opt/local/bin'),
        ),
		

		'db'=>array(
			'class'=>'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=rosyama',
			'enableParamLogging'=>true,
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'qwe1024',
			'charset' => 'utf8',
			'tablePrefix'=>'yii_'
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

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'arbprint@mail.ru',
		'YMapKey'=>'AEmk904BAAAAUCGkRAMAvTSoZfbI0tw8-95WnNcZkDQqXzAAAAAAAAAAAAB49EpXB9Mlar25hE3r2xY70FiRmQ==',
		//'layout'=>'startpage',

	),
);
