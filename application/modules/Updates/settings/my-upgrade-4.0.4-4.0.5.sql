--
-- Update module Updates
--

UPDATE `engine4_core_modules` SET `version` = '4.0.5'  WHERE `name` = 'updates';

DELETE FROM `engine4_core_mailtemplates` WHERE `type` = 'updates' LIMIT 1;
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES('updates', 'updates', '[updates]');