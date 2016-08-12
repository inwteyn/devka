UPDATE `engine4_core_modules` SET `version` = '4.3.3'  WHERE `name` = 'store';

INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ('store_admin_main_settings_currency', 'store', 'Currency', '', '{"route":"admin_default","module":"store", "controller":"currency"}', 'store_admin_main_settings', '', 10);

INSERT INTO `engine4_core_settings` (`name`, `value`) VALUES ('hestore.multi_currency.enabled', '1');