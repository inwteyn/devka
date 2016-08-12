--
-- Update module Usernotes
--

UPDATE `engine4_core_modules` SET `version` = '4.1.3p2'  WHERE `name` = 'usernotes';

UPDATE `engine4_core_menuitems` SET `label`='HE - Usernotes', `order` = '888' WHERE `name`='core_admin_main_plugins_usernotes';