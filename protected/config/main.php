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
				'profile'=>Array('Profile')
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
				  '/'=>'holes/index',
				  '<id:\d+>'=>'holes/view',
				  'map'=>'holes/map',
				  'page/<view:\w+>/' => 'site/page',
				  'userGroups'=>'userGroups',
				  'gii'=>'gii',
				  'profile'=>'profile',
				  'api/<id:\d+>'=>'api/index',
				  'api/my/<id:\d+>/update'=>'api/update',
				  'api/my/<id:\d+>/<type:[a-zA-Z0-9\_]+>'=>'api/setstate',
				   '<controller:\w+>'=>'<controller>/index',
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

		'loid' => array(
			'class' => 'application.extensions.lightopenid.loid',
		),
		
		 'eauth' => array(
			'class' => 'ext.eauth.EAuth',
			'popup' => true, // Use the popup window instead of redirecting.
			'services' => array( // You can change the providers and their classes.
				/*'google' => array(
					'class' => 'GoogleOpenIDService',
				),*/
				'yandex' => array(
					'class' => 'YandexOpenIDService',
				),
				'twitter' => array(
					// регистрация приложения: https://dev.twitter.com/apps/new
					'class' => 'TwitterOAuthService',
					'key' => 'l3EbqAebpIlPR1SAI5KANQ',
					'secret' => 'BPdGnGf0DvD5dLCkQKuyYKbzQ0ZnmvE2bXi795fUKk',
				),
				'google_oauth' => array(
					// регистрация приложения: https://code.google.com/apis/console/
					'class' => 'GoogleOAuthService',
					'client_id' => '433170583991.apps.googleusercontent.com',
					'client_secret' => 'FAZmvzjXjciqkENr4-a8c61q',
					'title' => 'Google',
				),
				'facebook' => array(
					// регистрация приложения: https://developers.facebook.com/apps/
					'class' => 'FacebookOAuthService',
					'client_id' => '321432484568993',
					'client_secret' => 'd8290ffe86ea761fa1125bbd86eea708',
				),
				'vkontakte' => array(
					// регистрация приложения: http://vkontakte.ru/editapp?act=create&site=1
					'class' => 'VKontakteOAuthService',
					'client_id' => '2794711',
					'client_secret' => 'YghxsVhTwKk5wmKb9WRj',
				),
				'mailru' => array(
					'class' => 'MailruOpenIDService', 
				),
				'livejournal' => array(
					'class' => 'LJOpenIDService', 
				),
				/*'mailru' => array(
					// регистрация приложения: http://api.mail.ru/sites/my/add
					'class' => 'MailruOAuthService',
					'client_id' => '...',
					'client_secret' => '...',
				),*/
				'moikrug' => array(
					// регистрация приложения: https://oauth.yandex.ru/client/my
					'class' => 'MoikrugOAuthService',
					'client_id' => '...',
					'client_secret' => '...',
				),
				'odnoklassniki' => array(
					// регистрация приложения: http://www.odnoklassniki.ru/dk?st.cmd=appsInfoMyDevList&st._aid=Apps_Info_MyDev
					'class' => 'OdnoklassnikiOAuthService',
					'client_id' => '...',
					'client_public' => '...',
					'client_secret' => '...',
					'title' => 'Однокл.',
				),
			),
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

			),
			'enabled'=>isset($_GET['testing'])?true:false,  // enable caching in non-debug mode  
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
