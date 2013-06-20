<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
include('appConfig.php');
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'My Console Application',
    'commandMap' => array(
        'migrate' => array(
            'class' => 'system.cli.commands.MigrateCommand',
            'migrationTable' => '{{migration}}'
        )
    ),
    // application components
    'components' => array(
        'db' => $db,
    ),
);