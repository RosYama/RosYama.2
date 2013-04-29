ALTER TABLE `yii_gibdd_heads` ADD `tel_chancery` VARCHAR( 255 ) NOT NULL AFTER
`tel_dover`;
ALTER TABLE `yii_gibdd_heads_buffer` ADD `tel_chancery` VARCHAR( 255 ) NOT NULL
AFTER `tel_dover`;
ALTER TABLE `yii_prosecutors` ADD `tel_chancery` VARCHAR( 255 ) NOT NULL AFTER
`preview_text`;
ALTER TABLE `yii_prosecutors_buffer` ADD `tel_chancery` VARCHAR( 255 ) NOT NULL
AFTER `preview_text`;
