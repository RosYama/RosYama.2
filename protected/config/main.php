<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Rosyama',
	'language'=>'ru',
	'defaultController'=>'holes',
	// preloading 'log' component
	//'layout'=>'startpage',
	'preload'=>array('log'),	
	

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.classes.*',
		'application.modules.userGroups.models.*',
        'application.modules.userGroups.components.*',
		'application.extensions.nestedset.*',
		'application.extensions.fpdf.*',
		'application.extensions.*',
		'application.helpers.*',
			),
	'modules'=>array(
		
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'password'=>'root',
				'ipFilters' => array('77.37.132.232', '195.91.137.124', '127.0.0.1'),
				'generatorPaths' => array(
				'ext.giix-core',
				),
			),		
			'userGroups'=>array(
				'accessCode'=>'12345',
				'salt'=>'111',				
			)
    ),
	// application components
	'components'=>array(
		'user'=>array(
      	'allowAutoLogin'=>true,
		'class'=>'userGroups.components.WebUserGroups',

		),
		// uncomment the following to enable URLs in path-format

        'urlManager'=>array(
			//'baseUrl'=>'/',
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'urlSuffix'=>'/',
			'rules'=>array(
				  
				  '<id:\d+>'=>'holes/view',
				  'map'=>'holes/map',
				  '<controller:\w+>/<id:\d+>'=>'<controller>/view',
				  '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				  '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

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

		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),

        'getprice'=>array(
			'class'=>'application.classes.CGetTourPrice',
			),

		'getmenu'=>array(
			'class'=>'application.classes.CLeftMenu',
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
			/*'routes'=>array(
                                'web'=>array(
                                        'class'=>'CWebLogRoute',
                                        'levels'=>'trace, info, error, warning',
                                        'categories'=>'system.db.*',
                                        'showInFireBug'=>false //true/falsefirebug only - turn off otherwise
                                ),
                                'file'=>array(
                                        'class'=>'CFileLogRoute',
                                        'levels'=>'error, warning, watch',
                                        'categories'=>'system.*',
                                ),
                                'profile'=>array(
                                    'class' => 'CProfileLogRoute',
                                    'report'=>'summary',
                                    ),
				// uncomment the following to show log messages on web pages

				array(
					'class'=>'CWebLogRoute',
				),

			),*/
			'enabled'=>YII_DEBUG,  // enable caching in non-debug mode
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
