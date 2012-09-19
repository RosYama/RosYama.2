ALTER TABLE `yii_hole_requests` ADD `response_requestid` INT NOT NULL DEFAULT '0' AFTER `date_sent`;
ALTER TABLE `yii_hole_types` ADD `dorogimos_id` VARCHAR( 255 ) NOT NULL AFTER `pdf_footer`;
ALTER TABLE `yii_usergroups_user_profile` ADD `request_phone` VARCHAR( 63 ) NOT NULL AFTER `request_address`;
CREATE TABLE IF NOT EXISTS `yii_usergroups_user_social_accounts` (`ug_id` INT NOT NULL , `service_id` INT NOT NULL , 
  `xml_id` VARCHAR( 255 ) NOT NULL , `external_auth_id` VARCHAR( 255 ) NOT NULL , PRIMARY KEY (  `ug_id` ,  `service_id` ) ) ENGINE = INNODB;
CREATE TABLE IF NOT EXISTS `yii_usergroups_social_services` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `service_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
  
INSERT INTO `yii_usergroups_social_services` (`id`, `name`, `service_name`) VALUES
  (1, 'yandex', 'OPENID#http://openid.yandex.ru/server/'),
  (2, 'twitter', 'Twitter'),
  (3, 'google_oauth', 'GoogleOAuth'),
  (4, 'facebook', 'Facebook'),
  (5, 'vkontakte', 'VKontakte'),
  (6, 'mailru', 'OPENID#http://openid.mail.ru/login'),
  (7, 'livejournal', 'OPENID#http://www.livejournal.com/openid/server.bml'),
  (8, 'moikrug', 'MyMailRu'),
  (9, 'odnoklassniki', 'odnoklassniki');
  
