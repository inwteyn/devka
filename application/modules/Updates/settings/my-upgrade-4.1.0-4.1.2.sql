--
-- Update module Updates
--

UPDATE `engine4_core_modules` SET `version` = '4.1.2'  WHERE `name` = 'updates';

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('Background Newsletter Campaign Sender', 'campaign_send', 'updates', 'Updates_Plugin_Job_Campaign', NULL, 1, 50, 1),
('Background Newsletter Updates Sender', 'updates_send', 'updates', 'Updates_Plugin_Job_Updates', NULL, 1, 50, 1);

DELETE FROM `engine4_core_tasks` WHERE `module` = 'updates';
INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`) VALUES ('Background Newsletter Sender', 'updates', 'Updates_Plugin_Task_Updates');
