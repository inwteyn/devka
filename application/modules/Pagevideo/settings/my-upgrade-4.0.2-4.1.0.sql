INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `enabled`, `multi`, `priority`) VALUES
('Page Video Encode', 'pagevideo_encode', 'pagevideo', 'Pagevideo_Plugin_Job_Encode', 1, 2, 100);

UPDATE `engine4_core_modules` SET `version` = '4.1.0'  WHERE `name` = 'pagevideo';