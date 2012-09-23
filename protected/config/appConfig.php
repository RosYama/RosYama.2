<?php 
$bd=array(
			'class'=>'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=develrosdostup',
			'emulatePrepare' => false,
			'username' => 'develrosdostup',
			'password' => 'qwer1234',
			'charset' => 'utf8',
			'tablePrefix'=>'yii_',
			'schemaCachingDuration'=>3600,
			'enableProfiling' => true,
            'enableParamLogging' => true,
		);
		
$socials=array( // You can change the providers and their classes.
				/*'google' => array(
					'class' => 'GoogleOpenIDService',
				),*/
				'yandex' => array(
					'class' => 'YandexOpenIDService',
				),
				'twitter' => array(
					// регистрация приложения: https://dev.twitter.com/apps/new
					'class' => 'TwitterOAuthService',
					'key' => 'MRPer4S0jcGnhQA8AwPQ', 
					'secret' => 'hW3rSqtagjFHClDXGu2m0uLkmf6TWMbePjQp7Z6ZeE', 
				),
				'google_oauth' => array(
					// регистрация приложения: https://code.google.com/apis/console/
					'class' => 'GoogleOAuthService',
					'client_id' => '1011257333158.apps.googleusercontent.com',
					'client_secret' => 'OVwt4LX24jPXEmbzF32ILP2J',
					'title' => 'Google',
				),
				'facebook' => array(
					// регистрация приложения: https://developers.facebook.com/apps/
					'class' => 'FacebookOAuthService',
					'client_id' => '115042318646426',
					'client_secret' => 'a43b65c1bbe998986b35cd70ec98061e',
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
			);	

$params=array(
		// this is used in contact page
		'adminEmail'=>'admin@rosdostup.ru',
		'YMapKey'=>'AKtzDVABAAAAbnnpfAIAZPFgkjAuQV52QNsahNkE7plr4F0AAAAAAAAAAAAyrGSHDyaYbEfL1i8AVZDhjT4VHQ==',
		//'layout'=>'startpage',

	);		

?>
