--
-- Update module Hecore
--

UPDATE `engine4_core_modules` SET `version` = '4.5.0p6'  WHERE `name` = 'highlights';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('user_profile_credits', 'user', 'Credit Profile','Highlights_Plugin_Menus','', 'user_profile', '', 6);
INSERT INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `displayable`, `attachable`) VALUES ('highlights', 'user', '{item:$subject} highlighted {item:$object}', 7, 0);