<?php
/**
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yiic message' command.
 */
return array(
	'sourcePath'=>'protected'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'userGroups',
	'messagePath'=>'protected'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'userGroups'.DIRECTORY_SEPARATOR.'messages',
	'languages'=>array('it'), // change this value to the one of the language you want to localize the module into.
	'fileTypes'=>array('php'),
	'exclude'=>array(
		'.svn',
		'/messages',
	),
);
