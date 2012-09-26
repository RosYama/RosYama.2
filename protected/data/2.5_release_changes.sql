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
  
CREATE TABLE IF NOT EXISTS `yii_mainmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` int(11) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `element` varchar(255) DEFAULT NULL,
  `elementmodel` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

INSERT INTO `yii_mainmenu` (`id`, `lft`, `rgt`, `level`, `name`, `type`,
`link`, `controller`, `action`, `element`, `elementmodel`) VALUES
(1, 0, 175, 0, 'root', 0, '', '', '', '0', ''),
(2, 145, 150, 1, 'Ямы', 0, '', '', '', NULL, ''),
(3, 146, 147, 2, 'Поиск ям', 0, '', 'Holes', 'Index', NULL, ''),
(4, 148, 149, 2, 'Карта', 0, '', 'Holes', 'Map', NULL, ''),
(5, 151, 152, 1, 'Новости', 0, '', 'News', 'Index', NULL, ''),
(6, 153, 162, 1, 'Информация', 0, '', NULL, NULL, NULL, ''),
(7, 154, 155, 2, 'О проекте', 0, '', 'Site', 'page', 'about', 'CViewAction'),
(8, 156, 157, 2, 'Статистика', 0, '', 'Statics', 'Index', '', ''),
(9, 158, 159, 2, 'Нормативы', 0, '', 'Site', 'page', 'regulations', 'CViewAction'),
(10, 160, 161, 2, 'Справочники', 0, '', 'Sprav', 'Index', '', ''),
(11, 163, 166, 1, 'Поддержка', 0, '', NULL, NULL, NULL, ''),
(12, 167, 174, 1, 'Чем помочь?', 0, '', NULL, NULL, NULL, ''),
(13, 168, 169, 2, 'Деньги', 0, '', 'Site', 'page', 'donations', 'CViewAction'),
(14, 170, 173, 2, 'Разработка', 0, '', NULL, NULL, NULL, ''),
(15, 171, 172, 3, 'API', 1, 'http://rosyama.ru/api/', '', '', '', ''),
(18, 164, 165, 2, 'FAQ', 0, '', 'Site', 'page', 'faq', 'CViewAction');
