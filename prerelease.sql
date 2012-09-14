ALTER TABLE `yii_hole_requests` ADD `response_requestid` INT NOT NULL DEFAULT '0' AFTER `date_sent`;
ALTER TABLE `yii_hole_types` ADD `dorogimos_id` VARCHAR( 255 ) NOT NULL AFTER `pdf_footer`;