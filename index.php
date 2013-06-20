<?php

setlocale(LC_TIME, array("ru_RU","rus_RUS"));

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii-last/framework/yii.php';

if($_SERVER['HTTP_HOST']!='xml.rosyama.ru' and $_SERVER['HTTP_HOST']!='xml.dev.rosyama.ru')
	$config=dirname(__FILE__).'/protected/config/main.php';
else 	
	$config=dirname(__FILE__).'/protected/config/xml.php';

// remove the following lines when in production mode
if (isset($_GET['testing']) && $_GET['testing']==1){
        defined('YII_DEBUG') or define('YII_DEBUG',true);
	// specify how many levels of call stack should be shown in each log message
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
}
else {
	defined('YII_DEBUG') or define('YII_DEBUG',false);
}


define('SITE_TEMPLATE_PATH', '/');
require_once($yii);
Yii::createWebApplication($config)->run();
