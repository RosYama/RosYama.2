<?php 
$db=array(
	'class'=>'CDbConnection',
	'connectionString' => 'mysql:host=localhost;dbname=rosyama',
	'emulatePrepare' => false,
	'username' => 'root',
	'password' => '123',
	'charset' => 'utf8',
	'tablePrefix'=>'yii_',
	'schemaCachingDuration'=>3600,
	'enableProfiling' => YII_DEBUG,
        'enableParamLogging' => YII_DEBUG,
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
			);	

$params=array(
		// this is used in contact page
		'adminEmail'=>'arbprint@mail.ru',
		'YMapKey'=>'AEmk904BAAAAUCGkRAMAvTSoZfbI0tw8-95WnNcZkDQqXzAAAAAAAAAAAAB49EpXB9Mlar25hE3r2xY70FiRmQ==',
		'dorogiMos'=>array(
				'login'=>'',
				'password' => '',
				'server' => '',
			),
	);		

?>