--
-- Update module Updates
--

UPDATE `engine4_core_modules` SET `version` = '4.0.2'  WHERE `name` = 'updates';

UPDATE `engine4_core_menuitems` SET `label`='Dashboard' WHERE `name`='updates_admin_main_general' AND `module`='updates';
UPDATE `engine4_core_menuitems` SET `label`='Newsletter Updates' WHERE `name`='core_admin_main_plugins_updates' AND `module`='updates';
UPDATE `engine4_core_menuitems` SET `label`='Newsletter Updates' WHERE `name`='user_settings_updates' AND `module`='updates';

UPDATE `engine4_core_content` SET `name`='updates.home-subscriber' WHERE `name`='updates.home-updates';