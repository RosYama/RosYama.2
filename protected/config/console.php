<?php

$mainConsoleConfig = array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Rosyama console Application',
    'commandMap'=>array(
        'migrate'=>array(
            'class'=>'system.cli.commands.MigrateCommand',
            'migrationTable'=>'{{migration}}'
        )
    ),
    'components' => array(
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
);

/**
 * Чтобы указать свои параметры, создайте файл dev.xml.php
 * Пример файла:
 
return CMap::mergeArray(
	$mainConsoleConfig,
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

$devPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'/dev.console.php';
if (file_exists($devPath))
{
	$mainConsoleConfig = require_once $devPath;
}
return $mainConsoleConfig;