<?php

// 000002
// add pseudo social service "forum"

$DB  =& Yii::app()->db;
$sql = $DB->createCommand("insert into ".$DB->tablePrefix."usergroups_social_services
	(`name`, `service_name`) values ('forum', 'forum')");
$sql->execute();
Yii::app()->cache->flush();

?>