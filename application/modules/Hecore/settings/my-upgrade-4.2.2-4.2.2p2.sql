--
-- Update module Hecore
--
CREATE TABLE `engine4_hecore_log` (
	`log_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`plugin` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`timestamp` DATETIME NOT NULL,
	`error_code` VARCHAR(6) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`message` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`trace` LONGTEXT NOT NULL COLLATE 'utf8_unicode_ci',
	`priority` SMALLINT(2) NOT NULL DEFAULT '6',
	`priorityName` VARCHAR(16) NOT NULL DEFAULT 'INFO' COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`log_id`),
	INDEX `user_id` (`user_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

UPDATE `engine4_core_modules` SET `version` = '4.2.2p2'  WHERE `name` = 'hecore';