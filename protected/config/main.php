<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
include ('appConfig.php');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'РосДоступ',
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
			),
			'comments'=>array(
				//you may override default config for all connecting models
				'defaultModelConfig' => array(
					//only registered users can post comments
					'registeredOnly' => true,
					'useCaptcha' => false,
					//allow comment tree
					'allowSubcommenting' => true,
					//display comments after moderation
					'premoderate' => false,
					//action for postig comment
					'postCommentAction' => 'comments/comment/postComment',
					//super user condition(display comment list in admin view and automoderate comments)
					'isSuperuser'=>'Yii::app()->user->isModer',
					//order direction for comments
					'orderComments'=>'ASC',					
				),
				//the models for commenting
				'commentableModels'=>array(
					//model with individual settings
					'Holes'=>array(
						'registeredOnly'=>true,
						'useCaptcha'=>false,
						'allowSubcommenting'=>true,
						//config for create link to view model page(page with comments)
						'pageUrl'=>array(
							'route'=>'holes/view',
							'data'=>array('id'=>'ID'),
						),
					),
					//model with default settings
					'ImpressionSet',
				),
				//config for user models, which is used in application
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
		// uncomment the following to enable URLs in path-format

        'urlManager'=>array(
			//'baseUrl'=>'/',
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'urlSuffix'=>'/',
			'rules'=>array(
				  '/'=>'holes/index',
				  '<id:\d+>'=>'holes/view',
				  'map/<userid:\d+>/'=>'holes/map',				  
				  'map'=>'holes/map',
				  'page/<view:\w+>/' => 'site/page',
				  'userGroups'=>'userGroups',
				  'gii'=>'gii',
				  'profile'=>'profile',
				  'sprav/<subject_id:\d+>/add/'=>'sprav/add',
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
			'services' => $socials,
		),

		'db'=>$bd,
		
		'cache'=>array(
            //'class'=>'system.caching.CApcCache',          // we use MemCache for RosDostup
            'class'=>'system.caching.CMemCache',          
            'servers'=>array(
                array('host'=>'localhost', 'weight'=>60),
            ),
		'useMemcached'=>true,
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
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                array(
                    'class' => 'application.extensions.pqp.PQPLogRoute',
                    'categories' => 'application.*, exception.*',
                ),
            ),
			'enabled'=>isset($_GET['testing'])?true:false,  // enable caching in non-debug mode  
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>$params,
);
