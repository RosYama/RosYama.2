<?php

class m130629_103731_bug_223 extends CDbMigration
{
	public function up()
	{
		$DB  =& Yii::app()->db;
		$sql = $DB->createCommand("insert into ".$DB->tablePrefix."usergroups_social_services
			(`name`, `service_name`) values ('forum', 'forum')");
		$sql->execute();
	}

	public function down()
	{
		$DB  =& Yii::app()->db;
		$sql = $DB->createCommand("delete from ".$DB->tablePrefix."usergroups_social_services
			where `name` = 'forum' and `service_name` = 'forum'");
		$sql->execute();
	}
}