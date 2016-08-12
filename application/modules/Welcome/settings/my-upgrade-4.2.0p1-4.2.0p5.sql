--
-- Update module Welcome
--

UPDATE `engine4_core_modules` SET `version` = '4.2.0p5'  WHERE `name` = 'welcome';

ALTER TABLE `engine4_welcome_steps`
	CHANGE COLUMN `link` `link` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `body`;
