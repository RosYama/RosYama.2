<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
include ('appConfig.php');

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
		

		'db'=>$db,

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
	'params'=>$params,
);
