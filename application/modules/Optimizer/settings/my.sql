INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('optimizer', 'Optimizer', 'Optimizer', '4.0.1', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('optimizer_plugins_settings', 'optimizer', 'HE - Optimizer', '', '{"route":"admin_default", "module": "optimizer", "controller": "settings", "action": "index"}', 'core_admin_main_plugins', NULL, 1, 0, 897);