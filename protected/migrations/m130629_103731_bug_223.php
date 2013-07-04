<?php

class m130629_103731_bug_223 extends CDbMigration
{
	public function up()
	{
		/*$DB  =& Yii::app()->db;
		$sql = $DB->createCommand("insert into ".$DB->tablePrefix."usergroups_social_services
			(`name`, `service_name`) values ('forum', 'forum')");
		$sql->execute();*/
		m130629_103731_bug_223::clearCache();
	}

	public function down()
	{
		/*$DB  =& Yii::app()->db;
		$sql = $DB->createCommand("delete from ".$DB->tablePrefix."usergroups_social_services
			where `name` = 'forum' and `service_name` = 'forum'");
		$sql->execute();*/
		m130629_103731_bug_223::clearCache();
	}

	protected static function clearCache()
	{
		$cam = new CAssetManager();
		$path = dirname(__FILE__).'/../../assets/';
		$cam->setBasePath($path);
		$cam->publish('extensions/eauth/assets/css/auth.css', false, -1, true);
		$cam->publish('extensions/eauth/assets/images/auth.png', false, -1, true);
		$cam->publish('extensions/eauth/assets/images/auth_gray.png', false, -1, true);
		/*
		$cache = new CApcCache();
		$cache->keyPrefix=Yii::app()->getId(); // возвращает не такой, как надо
		$cache->delete('EAuth.services');
		$cache->flush();
		*/
		if(function_exists('apc_clear_cache'))
		{
			apc_clear_cache();
		}
	}
}